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
            'visit'                 => $this->prop_visit,
            'reference_type'        => $this->reference_type,
            'reference'             => $this->prop_reference,
        ];
        return $arr;
    }
}
