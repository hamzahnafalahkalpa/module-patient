<?php

namespace Hanafalah\ModulePatient\Resources\Referral;

class ShowReferral extends ViewReferral
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'reference'             => $this->relationValidation('reference', function () {
                return $this->reference->toShowApi()->resolve();
            }),
            'visit_registration' => $this->relationValidation('visitRegistration', function () {
                return $this->visitRegistration->toShowApi()->resolve();
            })
        ];

        $arr = array_merge(parent::toArray($request), $arr);

        return $arr;
    }
}
