<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModulePatient\Contracts\ExternalReferral as ContractsExternalRefferal;

class ExternalReferral extends PackageManagement implements ContractsExternalRefferal
{
    protected array $__guard   = ['visit_patient_id'];
    protected array $__add     = [];
    protected string $__entity = 'ExternalReferral';
    public static $external_referral;

    protected array $__cache = [];

    public function prepareStoreExternalReferral($attributes = null)
    {
        $attributes ??= request()->all();

        $external = $this->ExternalReferralModel()->updateOrCreate(
            [
                "visit_patient_id" => $attributes['visit_patient_id']
            ],
            [
                "date"             => $attributes['date'],
                "doctor_name"      => $attributes['doctor_name'],
                "phone"            => $attributes['phone'],
                "facility_name"    => $attributes['facility_name'],
                "unit_name"        => $attributes['unit_name'],
                "initial_diagnose" => $attributes['initial_diagnose'],
                "note"             => $attributes['note'],
            ],
        );

        $referral      = $external->referral;
        $visit_patient = $external->visitPatient;
        $referral->setAttribute('prop_patient', $visit_patient->patient->getPropsKey());
        $referral->save();

        return static::$external_referral = $external;
    }
}
