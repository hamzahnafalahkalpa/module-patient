<?php

namespace Hanafalah\ModulePatient\Schemas;

use Dompdf\Css\Content\Attr;
use Hanafalah\ModuleMedicService\Enums\MedicServiceFlag;
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
use Hanafalah\ModulePatient\Resources\VisitRegistration\{
    ShowVisitRegistration,
    ViewVisitRegistration
};

class VisitRegistration extends ModulePatient implements ContractsVisitRegistration
{
    protected string $__entity = 'VisitRegistration';
    public static $visit_registration_model;

    protected array $__cache = [
        'index' => [
            'name'     => 'visit-registration',
            'tags'     => ['visit-registration', 'visit-registration-index'],
            'forever'  => true
        ],
        'show' => [
            'name'     => 'visit-registration-show',
            'tags'     => ['visit-registration', 'visit-registration-show'],
            'forever'  => true
        ]
    ];

    protected function createVisitPatient($attributes)
    {
        $attributes['flag'] ??= 'CLINICAL_VISIT';
        if (isset($attributes['visit_patient_id'])) {
            $guard = ['id'          => $attributes['visit_patient_id']];
            $add   = ['reported_at' => $attributes['reported_at'] ?? null];
        } else {
            $guard = [
                'patient_id'     => $attributes['patient_id'] ?? null,
                'parent_id'      => $attributes['parent_id'] ?? null,
                'reference_id'   => $attributes['reference_id'] ?? null,
                'reference_type' => $attributes['reference_type'] ?? null,
                'flag'           => $attributes['flag'],
                'reported_at'    => $attributes['reported_at'] ?? null
            ];
        }
        $class_basename = ($attributes['flag'] == 'CLINICAL_VISIT') ? 'VisitPatient' : 'PharmacySale';
        return $this->schemaContract(Str::snake($class_basename))
            ->{'prepareStore' . $class_basename}($this->mergeArray($add ?? [], $guard, [
                'payer_id' => $attributes['payer_id'] ?? null,
                'agent_id' => $attributes['agent_id'] ?? null,
                'patient_type_id' => $attributes['patient_type_id'] ?? null
            ]));
    }

    public function prepareStoreVisitRegistration(VisitRegistrationData $visit_registration_dto): Model{
        $attributes ??= request()->all();
        $visit_patient = $this->createVisitPatient($this->mergeArray([
            'patient_id'      => $attributes['patient_id'] ?? null,
            'payer_id'        => $attributes['payer_id'] ?? null,
            'agent_id'        => $attributes['agent_id'] ?? null,
            'patient_type_id' => $attributes['patient_type_id'] ?? null
        ], $attributes['visit_patient'] ?? []));


        static::$visit_registration_model = $this->newVisitRegistration($this->mergeArray([
            'visit_patient_id'    => $visit_patient->getKey(),
            'visit_patient_type'  => $visit_patient->getMorphClass(),
            'visit_patient_model' => $visit_patient
        ], $attributes));

        if ($visit_patient->flag == 'CLINICAL_VISIT') {
            $this->setReportTransactionVisitPatient($visit_patient);
        }

        return static::$visit_registration_model;
    }

    public function setReportTransactionVisitPatient($visit_patient)
    {
        $transaction                = $visit_patient->transaction;
        $transaction->reported_at ??= now();
        $transaction->save();
    }

    public function createVisitRegistration(array $attributes): Model
    {
        if (isset($attributes['id'])) {
            $guard = ['id' => $attributes['id'] ?? null];
        } else {
            $guard = [
                'visit_patient_id'   => $attributes['visit_patient_id'],
                'visit_patient_type' => $attributes['visit_patient_type'],
                'medic_service_id'   => $attributes['medic_service_id'],
                'referral_id'        => $attributes['referral_id'] ?? null
            ];
        }

        if (isset($attributes['head_doctor_id'])) {
            $attributes['head_doctor_type'] = app(config('module-patient.head_doctor'))->getMorphClass();
        }

        $visit_registration = $this->VisitRegistrationModel()->updateOrCreate($guard, [
            'visited_at'        => now(),
            'status'            => RegistrationStatus::DRAFT->value,
            'parent_id'         => $attributes['parent_id'] ?? null,
            'patient_type_id'   => $attributes['patient_type_id'] ?? null,
            'head_doctor_id'    => $attributes['head_doctor_id'] ?? null,
            'head_doctor_type'  => $attributes['head_doctor_type'] ?? null
        ]);

        $visit_patient = (!isset($attributes['visit_patient_model']))
            ? $visit_registration->visitPatient
            : $attributes['visit_patient_model'];

        $visit_patient_payment_summary        = $visit_patient->paymentSummary()->firstOrCreate();
        $visit_reg_payment_summary            = $visit_registration->paymentSummary;
        $visit_reg_payment_summary->parent_id = $visit_patient_payment_summary->getKey();
        $visit_reg_payment_summary->save();

        $visit_registration->name = $attributes['medic_service_name'];
        $visit_registration->setAttribute('medic_service', [
            'id'         => $attributes['medic_service_id'],
            'name'       => $attributes['medic_service_name'],
            'service_id' => $attributes['service_id']
        ]);
        $visit_registration->save();
        return $visit_registration;
    }

    public function newVisitRegistration(?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        if (!isset($attributes['medic_service_id'])) throw new \Exception('No medic service provided', 422);
        $attributes['service_id']         = $attributes['medic_service_id'];
        $medic_service                    = $this->getMedicServiceByServiceId($attributes['medic_service_id'])->reference;
        $attributes['medic_service_id']   = $medic_service->getKey();
        $attributes['medic_service_name'] = $medic_service->name;
        $visit_registration               = $this->createVisitRegistration($attributes);
        $transaction_visit_registration   = $visit_registration->transaction;

        $visit_patient                    = $visit_registration->visitPatient;
        $transaction_visit_patient        = $visit_patient->transaction;

        $transaction_visit_registration->parent_id = $transaction_visit_patient->getKey();
        $transaction_visit_registration->save();

        $visit_patient_payment_summary         = $visit_patient->paymentSummary;
        $attributes['visit_patient_type']      = $visit_patient->getMorphClass();

        $visit_payment_summary                 = $visit_registration->paymentSummary;
        $visit_payment_summary->transaction_id = $transaction_visit_registration->getKey();
        $visit_payment_summary->parent_id      = $attributes['payment_summary_parent_id'] ?? $visit_patient_payment_summary->getKey();
        $visit_payment_summary->name           = 'Total tagihan ' . $medic_service->name;
        $visit_payment_summary->save();
        $this->addTransactionIdTo($visit_patient, $visit_patient->transaction);

        if (in_array($medic_service->flag, [
            MedicServiceFlag::OUTPATIENT->value,
            MedicServiceFlag::MCU->value,
            MedicServiceFlag::LABORATORY->value,
            MedicServiceFlag::RADIOLOGY->value
        ])) {
            $visit_registration->pushActivity(VisitRegistrationActivity::POLI_EXAM->value, [VisitRegistrationActivityStatus::POLI_EXAM_QUEUE->value]);
            $this->appVisitPatientSchema()->preparePushLifeCycleActivity($visit_patient, $visit_registration, 'POLI_EXAM', [
                'POLI_EXAM_QUEUE' => 'Pasien dalam antrian ke poli ' . $medic_service->name
            ]);
        }
        $visit_examination_model = $this->appVisitExaminationSchema()->prepareStoreVisitExamination([
            'services' => $attributes['services'] ?? [],
            $visit_registration->getForeignKey() => $visit_registration->getKey(),
        ]);

        if (isset($attributes['head_doctor_id']) || isset($attributes['practitioner_id'])) {
            $this->appPractitionerEvaluationSchema()->prepareStorePractitionerEvaluation([
                'visit_examination_id' => $visit_examination_model->getKey(),
                'practitioner_id'      => $attributes['head_doctor_id'] ?? $attributes['practitioner_id'] ?? null,
                'role_as'              => $attributes['role_as'] ?? PIC::IS_PIC->value
            ]);
        }
        if (isset($attributes['medic_services']) && count($attributes['medic_services']) > 0) {
            $attributes['visit_registration_parent_id']        = $visit_registration->getKey();
            $attributes['visit_registration_medic_service_id'] = $visit_registration->medicService->service->getKey();
            $this->storeServices($attributes);
        }
        $visit_patient->refresh();
        $visit_registration->refresh();
        $visit_registration->load('paymentSummary');
        $this->forgetTags('visit-registration');
        return $visit_registration;
    }

    public function storeServices($attributes)
    {
        $visit_registrations = [];
        foreach ($attributes['medic_services'] as $medic_service) {
            $service_model = $this->ServiceModel()->with('reference')->findOrFail($medic_service['id']);
            $create = [
                'services'           => $medic_service['services'] ?? [],
                'visit_patient_id'   => $attributes['visit_patient_id'],
                'visit_patient_type' => $attributes['visit_patient_type'],
                'patient_type_id'    => $attributes['patient_type_id'] ?? null,
                'medic_service_id'   => $service_model->getKey()
            ];
            if ($service_model->getKey() != $attributes['visit_registration_medic_service_id']) {
                $create['parent_id'] = $attributes['visit_registration_parent_id'];
                $internal_referral = $this->InternalReferralModel()->where('medic_service_id', $medic_service['id'])
                    ->whereHas('referral', function ($query) use ($attributes) {
                        $query->where('visit_registration_id', $attributes['visit_registration_parent_id']);
                    })->first();
                if (!isset($internal_referral)) {
                    $referral_schema = $this->schemaContract('internal_referral');
                    $internal_referral = $referral_schema->prepareStoreInternalReferral([
                        'visit_registration_id' => $attributes['visit_registration_parent_id'],
                        'medic_service_id'      => $service_model->getKey()
                    ]);
                }
                $create['referral_id'] = $internal_referral->getKey();
            }
            $visit_registrations[] = $this->newVisitRegistration($create);
        }
        return $visit_registrations;
    }


    public function getVisitRegistration(): mixed
    {
        return static::$visit_registration_model;
    }

    public function prepareShowVisitRegistration(?Model $model = null): ?Model
    {
        $this->booting();

        $model ??= $this->getVisitRegistration();
        if (!isset($model)) {
            $id = request()->id;
            if (!isset(request()->id)) throw new \Exception('No id provided', 422);

            $model = $this->visitRegistration()->find($id);
        }
        $model->load($this->showUsingRelation());
        return static::$visit_registration_model = $model;
    }

    public function showVisitRegistration(?Model $model = null): array
    {
        return $this->transforming($this->__resources['show'], function () use ($model) {
            return $this->prepareShowVisitRegistration($model);
        });
    }

    public function storeVisitRegistration(): array
    {
        return $this->transaction(function () {
            return $this->showVisitRegistration($this->prepareStoreVisitRegistration());
        });
    }

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
                    MedicServiceFlag::INPATIENT,
                    MedicServiceFlag::VERLOS_KAMER,
                    MedicServiceFlag::OPERATING_ROOM
                ])) {
                    $query->with('visitExamination');
                }
            });
    }

    public function viewUsingRelation(): array
    {
        return [
            'medicService',
            'patientType',
            'visitPatient.patient'
        ];
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
    public function prepareVisitRegistrationPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): LengthAwarePaginator
    {
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
        return $this->commonPaginate($paginate_options);
    }

    public function viewVisitRegistrationPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null)
    {
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
        return $this->transforming($this->__resources['view'], function () use ($paginate_options) {
            return $this->prepareVisitRegistrationPaginate(...$this->arrayValues($paginate_options));
        });
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

    public function viewVisitRegistrationHistoryPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null)
    {
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
        return $this->transforming($this->__resources['view'], function () use ($paginate_options) {
            return $this->prepareVisitRegistrationHistoryPaginate(...$this->arrayValues($paginate_options));
        });
    }

    public function getVisitRegistrations()
    {
        $data = $this->visitRegistration()->with([
            'medicService.service',
            'patientType',
            'headDoctor',
            'visitPatient.patient'
        ])->whereHas('visitPatient', function ($q) {
            $q->where('patient_id', request()->patient_id);
        })->get();
        return $data;
    }

    public function addOrChange(?array $attributes = []): self
    {
        $this->updateOrCreate($attributes);
        return $this;
    }
}
