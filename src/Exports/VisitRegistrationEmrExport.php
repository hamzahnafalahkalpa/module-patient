<?php

namespace Hanafalah\ModulePatient\Exports;

use Hanafalah\LaravelSupport\Jobs\ProcessExportJob;
use Hanafalah\LaravelSupport\Models\Export\Export;
use Hanafalah\MicroTenant\Facades\MicroTenant;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class VisitRegistrationEmrExport
{
    /**
     * The schema instance (VisitRegistration schema).
     *
     * @var mixed
     */
    protected $schema;

    /**
     * Create a new export instance.
     *
     * @param mixed $schema The VisitRegistration schema instance
     */
    public function __construct($schema)
    {
        $this->schema = $schema;
    }

    /**
     * Handle the export request.
     * Creates an export record and dispatches the job.
     *
     * @return Export
     */
    public function handle(): Export
    {
        // Get the visit registration model from schema
        $visitRegistration = $this->schema->entityData();

        if (!$visitRegistration) {
            throw new \Exception('Visit registration not found');
        }

        // Get tenant ID
        $tenantId = tenancy()->tenant->id ?? null;

        if (!$tenantId) {
            throw new \Exception('Tenant context not found');
        }

        // Get current user ID (if available)
        $userId = auth()->user()->id ?? null;

        // Calculate expiration date (30 days from now)
        $expiresAt = now()->addDays(30);

        // Create export record
        $export = Export::create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'export_type' => 'VisitRegistrationEmr',
            'reference_type' => get_class($visitRegistration),
            'reference_id' => $visitRegistration->id,
            'status' => \Hanafalah\LaravelSupport\Enums\Export\ExportStatus::PENDING,
            'metadata' => [
                'visit_registration_code' => $visitRegistration->visit_registration_code ?? null,
            ],
            'expires_at' => $expiresAt,
        ]);

        // Dispatch job to process export
        ProcessExportJob::dispatch($export->id, $tenantId);

        return $export;
    }

    /**
     * Generate the PDF export.
     * Called by the ProcessExportJob.
     *
     * @param Export $export The export record
     * @return string The file path
     */
    public function generate(Export $export): string
    {
        // Load all necessary data
        $data = $this->loadData($export);

        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('module-patient::exports.emr.visit-registration', $data)
            ->setOptions([
                'enable_php' => true,
                'enable_remote' => true,
            ]);

        $dompdf = $pdf->getDomPDF();
        $pdf->render();

        // Add page footer with page numbers
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->get_font('Helvetica', 'normal');

        $canvas->page_text(
            40,
            820,
            "Halaman {PAGE_NUM} dari {PAGE_COUNT} | Dicetak pada " . date('d/m/Y H:i'),
            $font,
            9,
            [0, 0, 0]
        );

        // Save to storage
        $tenantId = $export->tenant_id;
        $timestamp = now()->format('YmdHis');
        $visitCode = $export->metadata['visit_registration_code'] ?? 'unknown';

        // Create directory structure: tenant_{id}/exports/{year}/{month}
        $year = now()->format('Y');
        $month = now()->format('m');
        $directory = "tenant_{$tenantId}/exports/{$year}/{$month}";

        // Ensure directory exists
        Storage::makeDirectory($directory);

        // File path and name
        $fileName = "emr_{$visitCode}_{$timestamp}.pdf";
        $filePath = "{$directory}/{$fileName}";

        // Save PDF to storage
        $fullPath = storage_path("app/{$filePath}");
        $pdf->save($fullPath);

        return $filePath;
    }

    /**
     * Load all data needed for the export.
     *
     * @param Export $export
     * @return array
     */
    protected function loadData(Export $export): array
    {
        // Get the visit registration from reference
        $visitRegistration = $export->reference;

        if (!$visitRegistration) {
            throw new \Exception('Visit registration reference not found');
        }

        // Load workspace (tenant clinic data)
        $workspace = tenancy()->tenant->reference;
        $workspace = $workspace->load($workspace->showUsingRelation());
        $workspace = $workspace->toShowApi()->resolve();

        // Load visit registration with all necessary relations
        $visitRegistration->load([
            'examinationSummary',
            'visitPatient' => function ($query) {
                $query->with([
                    'reference',
                    'patient' => function ($query) {
                        $query->with([
                            'reference.addresses',
                            'cardIdentities'
                        ]);
                    }
                ]);
            },
            'assessments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
        ]);

        // Get patient data
        $patient = $visitRegistration->visitPatient?->patient;
        $patientData = $patient ? $patient->toShowApi()->resolve() : null;

        // Get examination summary
        $examinationSummary = $visitRegistration->examinationSummary;

        // Get assessments (SOAP notes)
        $assessments = $visitRegistration->assessments()->get();

        // Get treatments
        $treatments = $visitRegistration->treatments()->get();

        // Get medications - try to get from assessments or pharmacy
        $medications = collect();
        // Note: Medication structure may vary - adjust based on actual implementation
        // This is a placeholder that should be adjusted based on actual data structure
        foreach ($assessments as $assessment) {
            if (isset($assessment->props['medications'])) {
                $medications = $medications->merge($assessment->props['medications']);
            }
        }

        return [
            'workspace' => $workspace,
            'visit_registration' => $visitRegistration,
            'examination_summary' => $examinationSummary,
            'patient' => $patientData,
            'assessments' => $assessments,
            'treatments' => $treatments,
            'medications' => $medications,
        ];
    }
}
