<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModulePatient\Contracts\Data\ExternalReferralData;
use Hanafalah\ModulePatient\Contracts\Schemas\ExternalReferral as ContractsExternalRefferal;
use Illuminate\Database\Eloquent\Model;

class ExternalReferral extends PackageManagement implements ContractsExternalRefferal
{
    protected string $__entity = 'ExternalReferral';
    public static $external_referral;

    public function prepareStoreExternalReferral(ExternalReferralData $external_referral_dto): Model{
        if (isset($external_referral_dto->id)){
            $guard = ['id' => $external_referral_dto->id];
        }else{
            $guard = ['visit_patient_id' => $external_referral_dto->visit_patient_id];
        }
        $external = $this->usingEntity()->updateOrCreate($guard,[
                "date"             => $external_referral_dto->date,
                "doctor_name"      => $external_referral_dto->doctor_name,
                "phone"            => $external_referral_dto->phone,
                "facility_name"    => $external_referral_dto->facility_name,
                "unit_name"        => $external_referral_dto->unit_name,
                "initial_diagnose" => $external_referral_dto->initial_diagnose,
                "note"             => $external_referral_dto->note
            ],
        );

        $this->fillingProps($external, $external_referral_dto->props);
        $external->save();

        $referral      = $external->referral;
        $visit_patient = $external->visitPatient;
        $referral->setAttribute('prop_patient', $visit_patient->prop_patient);
        $referral->save();

        return static::$external_referral = $external;
    }
}
