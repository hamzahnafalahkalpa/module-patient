<?php

namespace Hanafalah\ModulePatient\Schemas;

use Dompdf\Css\Content\Attr;
use Hanafalah\ModuleMedicService\Enums\Label;
use Illuminate\Database\Eloquent\{
    Builder,
    Model
};
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Str;
use Hanafalah\ModulePatient\{
    Contracts\Schemas\VisitRegistration as ContractsVisitRegistration,
    Enums\VisitRegistration\RegistrationStatus,
    ModulePatient
};
use Hanafalah\ModulePatient\Contracts\Data\VisitRegistrationData;
use Hanafalah\ModulePatient\Enums\{
    EvaluationEmployee\PIC,
    VisitRegistration\Activity as VisitRegistrationActivity,
    VisitRegistration\ActivityStatus as VisitRegistrationActivityStatus
};

class VisitRegistration extends ModulePatient implements ContractsVisitRegistration
{
    protected string $__entity = 'VisitRegistration';
    public static $visit_registration_model;

    protected array $__cache = [
        'index' => [
            'name'     => 'visit_registration',
            'tags'     => ['visit_registration', 'visit_registration-index'],
            'forever'  => true
        ],
        'show' => [
            'name'     => 'visit_registration-show',
            'tags'     => ['visit_registration', 'visit_registration-show'],
            'forever'  => true
        ]
    ];

    public function prepareStoreVisitRegistration(VisitRegistrationData $visit_registration_dto): Model{
        $visit_registration = $this->prepareStore($visit_registration_dto);

        // if ($visit_patient->flag == 'CLINICAL_VISIT') {
        //     $this->setReportTransactionVisitPatient($visit_patient);
        // }

        return static::$visit_registration_model = $visit_registration;
    }

    // public function setReportTransactionVisitPatient($visit_patient)
    // {
    //     $transaction                = $visit_patient->transaction;
    //     $transaction->reported_at ??= now();
    //     $transaction->save();
    // }

    public function prepareStore(VisitRegistrationData $visit_registration_dto): Model{
        $visit_registration = $this->createVisitRegistration($visit_registration_dto);
        $visit_patient      = $visit_registration_dto->visit_patient_model;

        if (isset($visit_registration_dto->visit_examination)){
            $visit_examination_dto = &$visit_registration_dto->visit_examination;
            $visit_examination_dto->visit_patient_id      = $visit_patient->getKey();
            $visit_examination_dto->visit_registration_id = $visit_registration->getKey();
            $visit_examination = $this->schemaContract('visit_examination')->prepareStoreVisitExamination($visit_examination_dto);
            $visit_registration_dto->props->props['prop_visit_examination'] = $visit_examination->toViewApi()->resolve();
        }

        // $visit_examination_model = $this->appVisitExaminationSchema()->prepareStoreVisitExamination([
        //     'services' => $attributes['services'] ?? [],
        //     $visit_registration->getForeignKey() => $visit_registration->getKey(),
        // ]);


        // if (isset($attributes['medic_services']) && count($attributes['medic_services']) > 0) {
        //     $attributes['visit_registration_parent_id']        = $visit_registration->getKey();
        //     $attributes['visit_registration_medic_service_id'] = $visit_registration->medicService->service->getKey();
        //     $this->storeServices($attributes);
        // }
        $this->fillingProps($visit_registration, $visit_registration_dto->props);
        $visit_registration->save();

        if (in_array($visit_registration->prop_medic_service['label'], [
            Label::OUTPATIENT->value, Label::MCU->value,
            Label::LABORATORY->value, Label::RADIOLOGY->value
        ])) {
            $visit_registration->pushActivity(VisitRegistrationActivity::POLI_EXAM->value, [VisitRegistrationActivityStatus::POLI_EXAM_QUEUE->value]);
            $this->schemaContract('visit_patient')->preparePushLifeCycleActivity($visit_patient, $visit_registration, 'POLI_EXAM', [
                'POLI_EXAM_QUEUE' => 'Pasien dalam antrian ke poli '.$visit_registration->prop_medic_service['name']
            ]);
        }

        return static::$visit_registration_model = $visit_registration;
    }

    public function createVisitRegistration(VisitRegistrationData &$visit_registration_dto): Model{
        $add = [
            'visited_at'        => now(),
            'name'              => $visit_registration_dto->name ?? null,
            'parent_id'         => $visit_registration_dto->parent_id ?? null
        ];

        $guard = [
            'id'                 => $visit_registration_dto->id ?? null,
            'visit_patient_id'   => $visit_registration_dto->visit_patient_id,
            'visit_patient_type' => $visit_registration_dto->visit_patient_type,
            'medic_service_id'   => $visit_registration_dto->medic_service_id,
            // 'referral_id'        => $visit_registration_dto->referral_id ?? null
        ];

        $visit_registration = $this->VisitRegistrationModel()->updateOrCreate($guard,$add);
        $visit_registration->load(['paymentSummary', 'transaction']);
        $visit_patient = $visit_registration_dto->visit_patient_model ??= $visit_registration->visitPatient;
        
        $trx_visit_patient                 = &$visit_patient->transaction;
        $trx_visit_registration            = &$visit_registration->transaction;
        $trx_visit_registration->parent_id = $trx_visit_patient->getKey();
        $trx_visit_registration->save();

        $vr_payment_summary                 = &$visit_registration->paymentSummary;
        $vr_payment_summary->parent_id      = $visit_patient->paymentSummary->getKey();
        $vr_payment_summary->transaction_id = $trx_visit_registration->getKey();
        $vr_payment_summary->name           = 'Total tagihan ' . $visit_registration_dto->name;
        $vr_payment_summary->save();

        $this->fillingProps($visit_registration, $visit_registration_dto->props);
        $visit_registration->save();
        return static::$visit_registration_model = $visit_registration;
    }

    // public function storeServices($attributes)
    // {
    //     $visit_registrations = [];
    //     foreach ($attributes['medic_services'] as $medic_service) {
    //         $service_model = $this->ServiceModel()->with('reference')->findOrFail($medic_service['id']);
    //         $create = [
    //             'services'           => $medic_service['services'] ?? [],
    //             'visit_patient_id'   => $attributes['visit_patient_id'],
    //             'visit_patient_type' => $attributes['visit_patient_type'],
    //             'patient_type_id'    => $attributes['patient_type_id'] ?? null,
    //             'medic_service_id'   => $service_model->getKey()
    //         ];
    //         if ($service_model->getKey() != $attributes['visit_registration_medic_service_id']) {
    //             $create['parent_id'] = $attributes['visit_registration_parent_id'];
    //             $internal_referral = $this->InternalReferralModel()->where('medic_service_id', $medic_service['id'])
    //                 ->whereHas('referral', function ($query) use ($attributes) {
    //                     $query->where('visit_registration_id', $attributes['visit_registration_parent_id']);
    //                 })->first();
    //             if (!isset($internal_referral)) {
    //                 $referral_schema = $this->schemaContract('internal_referral');
    //                 $internal_referral = $referral_schema->prepareStoreInternalReferral([
    //                     'visit_registration_id' => $attributes['visit_registration_parent_id'],
    //                     'medic_service_id'      => $service_model->getKey()
    //                 ]);
    //             }
    //             $create['referral_id'] = $internal_referral->getKey();
    //         }
    //         $visit_registrations[] = $this->prepareStore($create);
    //     }
    //     return $visit_registrations;
    // }

    public function visitRegistration(mixed $conditionals = null): Builder
    {
        return $this->VisitRegistrationModel()
            ->with('visitPatient.patient')
            ->conditionals($this->mergeCondition($conditionals ?? []))
            ->when(isset(request()->patient_id), function ($query) {
                $query->whereHasMorph('visitPatient', ['*'], function ($query) {
                    $query->where('patient_id', request()->patient_id);
                });
            })
            ->when(isset(request()->search_value), function ($query) {
                request()->merge([
                    'search_medical_record' => request()->search_value,
                    'search_name'           => request()->search_value,
                    'search_nik'            => request()->search_value,
                    'search_crew_id'        => request()->search_value,
                    'search_dob'            => request()->search_value,
                    'search_created_at'     => null,
                    'search_value'          => null
                ]);
                $query->whereHasMorph('visitPatient', ['*'], function ($query) {
                    $query->whereHas('patient', fn($q) => $q->withParameters('or'));
                });
            })
            ->when(isset(request()->search_created_at) || isset(request()->search_service_label_id), function ($query) {
                $query->withParameters();
            })
            ->when(isset(request()->flag), function ($query) {
                $medic_services    = $this->MedicServiceModel()->flagIn(request()->flag)->get();
                $medic_service_ids = $medic_services->pluck('id');
                $query->whereIn('medic_service_id', $medic_service_ids);
                if (!in_array(request()->flag, [
                    Label::INPATIENT,
                    Label::VERLOS_KAMER,
                    Label::OPERATING_ROOM
                ])) {
                    $query->with('visitExamination');
                }
            });
    }

    public function visitRegistrationCancellation(?array $attributes): Model
    {
        $attributes ??= request()->all();
        $visitRegistration         = $this->VisitRegistrationModel()->find($attributes['visit_registration_id']);
        $visitRegistration->status = RegistrationStatus::CANCELLED->value;
        $visitRegistration->save();

        $visitRegistration->pushActivity(VisitRegistrationActivity::POLI_SESSION->value, [VisitRegistrationActivityStatus::POLI_SESSION_CANCEL->value]);

        return $visitRegistration;
    }

    public function commonPaginate($paginate_options): LengthAwarePaginator
    {
        return $this->visitRegistration()->with($this->viewUsingRelation())
            ->when(isset(request()->medic_service_id), function ($query) {
                $medic_service_id = $this->mustArray(request()->medic_service_id);
                $services = $this->ServiceModel()->whereIn('id', $medic_service_id)->get();
                $medic_service_ids = \array_column($services->toArray(), 'reference_id');
                $query->whereIn('medic_service_id', $medic_service_ids);
            })
            ->when(isset(request()->activity), function ($query) {
                $query->whereJsonContains('props->prop_activity');
            })
            ->where(function ($q) {
                (isset(request()->history))
                    ? $q->whereIn("status", [RegistrationStatus::CANCELLED->value, RegistrationStatus::COMPLETED->value])
                    : $q->whereNotIn("status", [RegistrationStatus::CANCELLED->value, RegistrationStatus::COMPLETED->value]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(
                ...$this->arrayValues($paginate_options)
            )->appends(request()->all());
    }

    public function historyVisit($paginate_options): LengthAwarePaginator
    {
        return $this->visitRegistration()->with($this->viewUsingRelation())
            ->when(isset(request()->medic_service_id), function ($query) {
                $medic_service_id = $this->mustArray(request()->medic_service_id);
                $services = $this->ServiceModel()->whereIn('id', $medic_service_id)->get();
                $medic_service_ids = \array_column($services->toArray(), 'reference_id');
                $query->whereIn('medic_service_id', $medic_service_ids);
            })
            ->when(isset(request()->activity), function ($query) {
                $query->whereJsonContains('props->prop_activity');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(
                ...$this->arrayValues($paginate_options)
            )->appends(request()->all());
    }
    public function prepareVisitRegistrationHistoryPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): LengthAwarePaginator
    {
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
        return $this->historyVisit($paginate_options);
    }
}
