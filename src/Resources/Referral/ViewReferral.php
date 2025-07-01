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
            'reference_type'        => $this->reference_type,
            'reference'             => $this->relationValidation('reference', function () {
                return $this->reference->toViewApi()->resolve();
            }),
            'visit_registration'    => $this->relationValidation('visitRegistration', function () {
                return $this->visitRegistration->toViewApi()->resolve();
            })
        ];

        return $arr;
    }
}
