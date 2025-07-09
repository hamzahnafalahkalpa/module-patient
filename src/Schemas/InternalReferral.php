<?php

namespace Hanafalah\ModulePatient\Schemas;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModulePatient\Contracts\Data\InternalReferralData;
use Hanafalah\ModulePatient\Contracts\Schemas\InternalReferral as ContractsInternalRefferal;

class InternalReferral extends PackageManagement implements ContractsInternalRefferal
{
    protected string $__entity = 'InternalReferral';
    public static $internal_referral;

    public function prepareStoreInternalReferral(InternalReferralData $internal_referral_dto): Model{
        $attributes ??= request()->all();
        $medic_service     = $this->ServiceModel()->findOrFail($attributes['medic_service_id']);
        $internal_referral = $this->InternalReferralModel()->whereHas('referral', fn($q) => $q->where('visit_registration_id', $attributes['visit_registration_id']))
            ->where('medic_service_id', $medic_service->reference_id)->first();
        if (!isset($internal_referral)) {
            $internal_referral = $this->internalReferral()->firstOrCreate([
                'medic_service_id' => $medic_service->reference_id
            ]);

            $referral = $internal_referral->referral()->firstOrCreate([
                'visit_registration_id' => $attributes['visit_registration_id']
            ]);

            $visit_registration = $this->VisitRegistrationModel()->find($attributes['visit_registration_id']);
            $referral->sync($medic_service, [
                'id'         => $attributes['medic_service_id'],
                'name'       => $medic_service->name,
                'service_id' => $medic_service->reference_id
            ]);
            $referral->setAttribute('prop_patient', $visit_registration->visitPatient->patient->getPropsKey());
            $referral->save();
        }

        $internal_referral->description = $attributes['description'] ?? null;
        $internal_referral->is_isolation = $attributes['is_isolation'] ?? false;
        $internal_referral->save();

        return static::$internal_referral = $internal_referral;
    }
}
