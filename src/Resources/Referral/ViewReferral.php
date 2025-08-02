<?php

namespace Hanafalah\ModulePatient\Resources\Referral;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewReferral extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'                    => $this->id,
            'referral_code'         => $this->referral_code,
            'status'                => $this->status,
            'patient'               => $this->prop_patient,
            'visit_type'            => $this->visit_type,
            'visit_id'              => $this->visit_id,
            'visit'                 => $this->prop_visit,
            'visit_registration'    => $this->relationValidation('visitRegistration',function(){
                return $this->visitRegistration->toShowApi()->resolve();
            },$this->prop_visit_registration),
            'reference_type'        => $this->reference_type,
            'reference'             => $this->prop_reference,
            'medic_service_id'      => $this->medic_service_id,
            'medic_service'         => $this->prop_medic_service,
            'external_referral'     => $this->external_referral
        ];
        return $arr;
    }
}