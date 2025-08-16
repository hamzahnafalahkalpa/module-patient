<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModuleMedicService\Enums\Label;
use Illuminate\Database\Eloquent\{
    Model
};
use Hanafalah\ModulePatient\{
    Enums\VisitExamination\CommitStatus,
    Enums\VisitExamination\ExaminationStatus,
    Enums\VisitPatient\VisitStatus,
    Enums\VisitRegistration\RegistrationStatus,
    ModulePatient,
    Enums\EvaluationEmployee\Commit,
    Enums\VisitExamination\Activity,
    Enums\VisitExamination\ActivityStatus,
    Contracts\Schemas\VisitExamination as ContractsVisitExamination
};
use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationData;
use Hanafalah\ModulePatient\Enums\{
    VisitRegistration\Activity as VisitRegistrationActivity,
    VisitRegistration\ActivityStatus as VisitRegistrationActivityStatus
};

class VisitExamination extends ModulePatient implements ContractsVisitExamination
{
    protected string $__entity = 'VisitExamination';
    protected mixed $__order_by_created_at = 'desc'; //asc, desc, false
    public $visit_examination_model;

    public function prepareCommitVisitExamination(?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        $visit_examination = $this->VisitExaminationModel()->find($attributes['visit_examination_id']);
        $visit_examination->is_commit = Commit::COMMIT->value;
        $visit_examination->save();

        //PUSH ACTIVITY FOR COMMITED
        $visit_examination->pushActivity(Activity::VISITATION->value, [ActivityStatus::VISITED->value]);

        $assessments = $visit_examination->assessments()->whereIn('morph', ['InitialDiagnose', 'SecondaryDiagnose', 'PrimaryDiagnose'])->get();
        foreach ($assessments as $assessment) {
            $assessment = $this->{$assessment->morph . 'Model'}()->find($assessment->getKey());
            $assessment->reporting();
        }
        return $visit_examination;
    }

    public function commitVisitExamination(): array{
        return $this->transaction(function () {
            return $this->showVisitExamination($this->prepareCommitVisitExamination());
        });
    }

    public function prepareStoreVisitExamination(VisitExaminationData $visit_examination_dto): Model{
        $visit_registration = $visit_examination_dto->visit_registration_model 
                              ?? $this->VisitRegistrationModel()->with('medicService')
                                      ->findOrFail($visit_examination_dto->visit_registration_id);

        $medic_service = $visit_registration->medicService;
        if (!isset($medic_service)) throw new \Exception("medic service not found");

        $add = [
            'visit_registration_id' => $visit_examination_dto->visit_registration_id,
            'visit_patient_id'      => $visit_examination_dto->visit_patient_id,
        ];

        if (isset($visit_examination_dto->id)){
            $guard = ['id' => $visit_examination_dto->id];
            $create = [$guard,$add];
        }else{
            $create = [$add];
        }

        $visit_examination  = $this->usingEntity()->firstOrCreate(...$create);
        $visit_examination->pushActivity(Activity::VISITATION->value, [ActivityStatus::VISIT_CREATED->value, ActivityStatus::VISITING->value]);
        
        //SET ASSESSMENT
        if (isset($visit_examination_dto->examination)){
            $examination_dto = &$visit_examination_dto->examination;
            $examination_dto->visit_examination_id ??= $visit_examination->getKey();
            $examination_dto->visit_examination_model ??= $visit_examination;
            $examination_dto->visit_patient_model ??= $visit_examination_dto->visit_patient_model;
            $examination_dto->visit_registration_model ??= $visit_examination_dto->visit_registration_model;
            $examination_dto->patient_model ??= $visit_examination_dto->patient_model;
            $response = $this->schemaContract('examination')->prepareStoreExamination($examination_dto);
            $visit_examination_dto->props->props['examination'] = $response;
        }
        
        if (in_array($medic_service->flag, [Label::OUTPATIENT->value, Label::MCU->value])) {
            //ADD DEFAULT SCREENING
            // $screenings = [];
            // $screening_models = $this->ScreeningModel()->whereHas('hasServices', function ($query) use ($medic_service) {
            //     $query->where('service_id', $medic_service->service->getKey());
            // })->get();
            // if (isset($screening_models) && count($screening_models) > 0) {
            //     foreach ($screening_models as $screening) {
            //         $screenings[] = [
            //             $screening->getKeyName() => $screening->getKey(),
            //             'name'                   => $screening->name
            //         ];
            //     }
            //     $visit_examination->setAttribute('screenings', $screenings);
            //     $visit_examination->save();
            // }
        }

        // if (isset($visit_examination_dto['services']) && count($attributes['services']) > 0) {
        //     $attributes['medic_service_flag'] ??= $medic_service->flag;
        //     $this->storeServices($visit_examination, $attributes);
        // }

        $this->fillingProps($visit_examination, $visit_examination_dto->props);
        $visit_examination->save();

        return $this->visit_examination_model = $visit_examination;
    }

    public function storeServices($visit_examination, $attributes)
    {
        $visit_registration  = $visit_examination->visitRegistration;
        $prop_service_labels = $visit_registration->prop_service_labels ?? [];
        $service_label_ids   = $this->pluckColumn($prop_service_labels, 'id');

        $visit_patient       = $visit_registration->visitPatient;
        $transaction         = $visit_patient->transaction;
        $examination_summary = $visit_examination->examinationSummary;
        $patient             = $visit_patient->patient;
        $patient_summary     = $patient->patientSummary()->firstOrCreate([
            'reference_id'   => $patient->reference_id,
            'reference_type' => $patient->reference_type
        ]);
        switch ($attributes['medic_service_flag']) {
            case Label::LABORATORY->value:
                $schema = 'lab_treatment';
                break;
            case Label::RADIOLOGY->value:
                $schema = 'radiology_treatment';
                break;
            case Label::OUTPATIENT->value:
                $schema = 'clinical_treatment';
                break;
            case Label::MCU->value:
                $schema = 'clinical_treatment';
                break;
        }
        $examination_treatment_schema = $this->schemaContract($schema);
        $transaction_item_schema = $this->schemaContract('transaction_item');
        foreach ($attributes['services'] as $service_attr) {
            if (!is_array($service_attr)) {
                $service_attr = [
                    'id'   => $service_attr,
                    'qty'  => 1
                ];
            }
            $service_attr['qty'] ??= 1;

            $service = $this->TreatmentModel()->findOrFail($service_attr['id']);
            if (in_array($service->reference_type, [
                $this->MedicalTreatmentModel()->getMorphClass(),
                $this->LaboratoriumModel()->getMorphClass(),
                $this->RadiologyModel()->getMorphClass()
            ])) {
                $examination_treatment_schema->prepareStore([
                    'visit_examination_id'   => $service_attr['visit_examination_id'] ?? $visit_examination->getKey(),
                    'examination_summary_id' => $service_attr['examination_summary_id'] ?? $examination_summary->getKey(),
                    'patient_summary_id'     => $service_attr['patient_summary_id'] ?? $patient_summary->getKey(),
                    'treatment_id'           => $service->getKey(),
                    'qty'                    => $service_attr['qty'] ?? 1,
                    'price'                  => $service_attr['price'] ?? null
                ]);
                if (isset($service->service_label)) {
                    $search = $this->searchArray($service_label_ids, $service->service_label['id']);

                    $service_label = $service->service_label;
                    if (\is_numeric($search)) {
                        $service_label = &$prop_service_labels[$search];
                    } else {
                        $service_label = [
                            'id'          => $service_label['id'],
                            'name'        => $service_label['name'],
                            'treatments'  => []
                        ];
                        $service_label_ids[] = $service_label['id'];
                    }

                    $service_label['treatments'][] = [
                        "id"     => $service->getKey(),
                        "name"   => $service->name,
                        "status" => 'DRAFT'
                    ];

                    if (!is_numeric($search)) $prop_service_labels[] = $service_label;
                }
            } else {
                $payment_summary = $transaction->paymentSummary;

                $transaction_item_schema->prepareStoreTransactionItem([
                    'transaction_id'          => $transaction->getKey(),
                    'item_type'               => $service->reference_type,
                    'item_id'                 => $service->reference_id,
                    'item_name'               => $service->name,
                    'payment_detail'          => [
                        'payment_summary_id'  => $payment_summary->id,
                        'qty'                 => $service_attr['qty'] ?? 1,
                        'amount'              => $service_attr['amount'] ?? null,
                        'debt'                => $service_attr['debt'] ?? null,
                        'price'               => $service->price ?? 0
                    ]
                ]);
            }
        }
        if (count($prop_service_labels) > 0) {
            $visit_registration->setAttribute('prop_service_labels', $prop_service_labels);
            $visit_registration->setAttribute('prop_service_label_ids', \implode($service_label_ids));
            $visit_registration->save();
        }
    }
    public function visitExaminationCancelation(?array $attributes = null)
    {
        $attributes ??= request()->all();
        $visit_examination = $this->prepareShowVisitExamination([
            "id" => $attributes['visit_examination_id']
        ]);

        if (!isset($visit_examination)) throw new \Exception("Data Examination Tidak Di Temukan");

        // CANCELLATION VISIT EXAMINATION
        $visit_examination->status = ExaminationStatus::CANCELLED->value;
        $visit_examination->save();
        $visit_examination->pushActivity(Activity::VISITATION->value, [ActivityStatus::CANCELLED->value]);

        $visit_registration = $visit_examination->visitRegistration;
        if (!isset($visit_registration)) throw new \Exception("Data Visit Registration Tidak Di Temukan");

        $visit_registration = $this->schemaContract('visit_registration')->visitRegistrationCancellation([
            "visit_registration_id" => $visit_registration->getKey()
        ]);

        $visit_patient = $visit_registration->visitPatient;
        if (!isset($visit_patient)) throw new \Exception("Data Visit Patient Tidak Ditemukan");

        $visit_patient->load([
            "visitRegistrations" => fn($q) => $q->whereIn("status", [
                RegistrationStatus::PROCESSING->value,
                RegistrationStatus::DRAFT->value
            ])
        ]);

        if (empty($visit_patient->visitRegistrations)) {
            $visit_patient->status = VisitStatus::CANCELLED->value;
            $visit_patient->saveQuietly();
        }

        return $visit_patient;
    }

    public function visitExaminationDoneProcess(?array $attributes = null)
    {
        $attributes ??= request()->all();
        $visit_examination = $this->prepareShowVisitExamination([
            "id" => $attributes['visit_examination_id']
        ]);
        if (isset($visit_examination)) {
            $visit_examination->is_commit = CommitStatus::COMMITED->value;
            $visit_examination->save();

            if ($visit_examination->is_commit == CommitStatus::COMMITED->value) {
                $visit_registration = $visit_examination->visitRegistration;

                if (isset($visit_registration)) {
                    $visit_registration->status = RegistrationStatus::COMPLETED->value;
                    $visit_registration->save();

                    $visit_registration->pushActivity(VisitRegistrationActivity::POLI_SESSION->value, [VisitRegistrationActivityStatus::POLI_SESSION_END->value]);
                }

                $visit_examination->pushActivity(Activity::VISITATION->value, [ActivityStatus::VISITED->value]);
                $visit_examination->reported_at = now();
                $visit_examination->status = ExaminationStatus::VISITED->value;
                $visit_examination->save();

                $visit_examination->pushActivity(Activity::VISITATION->value, [ActivityStatus::VISITED->value]);

                return $visit_examination;
            } else {
                throw new \Exception("Harap Commit terlebih dahulu sebelum penyelesaian patient!");
            }
        } else {
            throw new \Exception("Visit Examination Not Found");
        }
    }
}
