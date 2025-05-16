<?php

namespace Hanafalah\ModulePatient\Schemas;

use Illuminate\Database\Eloquent\{Builder, Model};
use Hanafalah\ModulePatient\{
    Contracts\Schemas\Referral as ContractsReferral,
    Enums\VisitRegistration\ActivityStatus,
    Enums\VisitRegistration\Activity,
    Resources\Referral\ViewReferral,
    Enums\Referral\Status,
    ModulePatient
};

class Referral extends ModulePatient implements ContractsReferral
{
    protected array $__guard   = ['id'];
    protected array $__add     = ['reference_id', 'reference_type', 'status'];
    protected string $__entity = 'Referral';

    public static $referral_model;

    protected array $__resources = [
        "view" => ViewReferral::class,
    ];

    protected array $__cache = [
        'index' => [
            'name'     => 'referral',
            'tags'     => ['referral', 'referral-index'],
            'duration' => 60
        ]
    ];

    public function prepareStoreReferral(?array $attributes = null): Model|null
    {
        $attributes ??= request()->all();

        if (isset($attributes['visit_examination_id']) || isset($attributes['referral_id'])) {
            $source = (isset($attributes['visit_examination_id']))
                ? $this->VisitExaminationModel()->findOrFail($attributes['visit_examination_id'])
                : $this->referral()->findOrFail($attributes['referral_id']);

            $visitRegistration = $source->visitRegistration;
            if (isset($visitRegistration)) {
                $referral_type = $attributes['flag'] ?? "internal";
                if (isset($attributes['flag']) && $referral_type == "external") {
                    $referralSide = $this->ExternalReferralModel()->create([
                        "visit_patient_id" => $visitRegistration->visit_patient_id,
                        "date"             => $attributes['date'],
                        "doctor_name"      => $attributes['doctor_name'],
                        "phone"            => $attributes['phone'],
                        "unit_name"        => $attributes['unit_name'],
                        "initial_diagnose" => $attributes['diagnose'],
                        "note"             => $attributes['note']
                    ]);
                } else {
                    //FOR INTERNAL CASE
                    $service      = $this->ServiceModel()->findOrFail($attributes['medic_service_id']);
                    $referralSide = $this->InternalReferralModel()->create([
                        "medic_service_id" => $service->reference_id
                    ]);
                    $service_id = $service->getKey();
                }
                if (!isset($referralSide)) throw new \Exception("Failed to create referral");

                $referral = $this->ReferralModel()->firstOrCreate([
                    'reference_id'          => $referralSide->getKey(),
                    'reference_type'        => $referralSide->getMorphClass(),
                    'visit_registration_id' => $visitRegistration->getKey()
                ]);
                $visit_patient = $visitRegistration->visitPatient;

                if ($referral_type == 'internal') {
                    $visit_patient_schema = $this->appVisitPatientSchema();

                    $this->appVisitRegistrationSchema()->newVisitRegistration([
                        'visit_patient_id'   => $visit_patient->getKey(),
                        'visit_patient_type' => $visit_patient->getMorphClass(),
                        'medic_service_id'   => $service_id ?? $visitRegistration->medicService->service->getKey(),
                        'referral_id'        => $referral->getKey(),
                        'head_doctor_id'     => $attributes['head_doctor_id'] ?? null,
                        'head_doctor_type'   => app(config('module-patient.practitioner'))->getMorphClass(),
                        'patient_type_id'    => $visitRegistration->patient_type_id,
                        'parent_id'          => $visitRegistration->getKey(),
                        'services'           => $attributes['services'] ?? []
                    ]);

                    $referral->pushActivity(Activity::REFERRAL_POLI->value, ActivityStatus::REFERRAL_PROCESSED->value);
                    $visit_patient_schema->preparePushLifeCycleActivity($visit_patient, $referral, 'REFERRAL_POLI', ['REFERRAL_CREATED']);
                    $referral->status = Status::PROCESS->value;

                    if (isset($attributes['visit_examination_id'])) {
                        $referral->pushActivity(Activity::REFERRAL_POLI->value, ActivityStatus::REFERRAL_CREATED->value);
                        $visit_patient_schema->preparePushLifeCycleActivity($visit_patient, $referral, 'REFERRAL_POLI', ['REFERRAL_CREATED']);

                        $referral->reference_id   = $referralSide->getKey();
                        $referral->reference_type = $referralSide->getMorphClass();
                        $referral->save();
                    }
                }

                $referral->setAttribute('prop_patient', $visit_patient->patient->getPropsKey());
                $referral->save();
                return $referral;
            } else throw new \Exception("Data Registrasi Tidak ditemukan");
        }
        throw new \Exception("Data Visit Pemeriksaan Tidak ditemukan");
    }

    public function referral(mixed $conditionals = null): Builder
    {
        $this->booting();
        return $this->ReferralModel()->conditionals($conditionals)->withParameters('or');
    }

    public function preparePaginateReferral(): object
    {
        $attributes ??= request()->all();
        return $this->referral(function ($q) use ($attributes) {
            $q->with([
                'visitRegistration' => function ($q) {
                    $q->with("visitPatient.patient", "medicService");
                },
                'reference' => function ($q) {
                    $q->constrain([
                        get_class($this->InternalReferralModel()) => function ($query) {
                            $query->with('medicService');
                        }
                    ]);
                }
            ])->whereHas("visitRegistration.visitExamination", function ($q) use ($attributes) {
                if (isset($attributes['visit_examination_id'])) $q->where("id", $attributes['visit_examination_id']);
            })->where(function ($q) {
                if (request()->is_external) {
                    $q->where("reference_type", $this->ExternalReferralModel()->getMorphClass());
                } else {
                    if (isset($attributes['visit_examination_id'])) {
                        $q->where("reference_type", $this->InternalReferralModel()->getMorphClass());
                    }
                }
            });
        })->paginate(request('per_page'))->appends(request()->all());
    }

    public function paginateReferral()
    {
        return $this->transforming($this->__resources['view'], function () {
            return $this->preparePaginateReferral();
        });
    }

    public function addOrChange(?array $attributes = []): self
    {
        $this->updateOrCreate($attributes);
        return $this;
    }
}
