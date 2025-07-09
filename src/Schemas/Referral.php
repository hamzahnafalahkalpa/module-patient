<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModulePatient\{
    Contracts\Schemas\Referral as ContractsReferral,
    Enums\VisitRegistration\ActivityStatus,
    Enums\VisitRegistration\Activity,
    Enums\Referral\Status,
    ModulePatient
};
use Hanafalah\ModulePatient\Contracts\Data\ReferralData;
use Illuminate\Database\Eloquent\Model;

class Referral extends ModulePatient implements ContractsReferral
{
    protected string $__entity = 'Referral';
    public static $referral_model;

    protected array $__cache = [
        'index' => [
            'name'     => 'referral',
            'tags'     => ['referral', 'referral-index'],
            'duration' => 60
        ]
    ];

    public function prepareStoreReferral(ReferralData $referral_dto): Model{
        $add = [
            'reference_type' => $referral_dto->reference_type, 
            'reference_id'   => $referral_dto->reference_id, 
            'visit_type'     => $referral_dto->visit_type, 
            'visit_id'       => $referral_dto->visit_id
        ];

        if (isset($referral_dto->id)){
            $guard = ['id' => $referral_dto->id];
            $create = [$guard,$add];
        }else{
            $create = [$add];
        }
        $referral = $this->usingEntity()->updateOrCreate(...$create);

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
}
