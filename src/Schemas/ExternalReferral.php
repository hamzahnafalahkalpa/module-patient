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

    public function prepareStore(ExternalReferralData $external_referral_dto): Model{
        return $this->prepareStoreExternalReferral($external_referral_dto);
    }

    public function prepareStoreExternalReferral(ExternalReferralData $external_referral_dto): Model{
        $external = $this->usingEntity()->updateOrCreate([
                'id'               => $external_referral_dto->id ?? null,
            ],[
                "date"             => $external_referral_dto->date,
                "doctor_name"      => $external_referral_dto->doctor_name,
                "phone"            => $external_referral_dto->phone,
                "facility_name"    => $external_referral_dto->facility_name,
                "unit_name"        => $external_referral_dto->unit_name,
                "initial_diagnose" => $external_referral_dto->initial_diagnose,
                "primary_diagnose" => $external_referral_dto->primary_diagnose,
                "note"             => $external_referral_dto->note
            ],
        );

        $this->fillingProps($external, $external_referral_dto->props);
        $external->save();
        return static::$external_referral = $external;
    }
}
