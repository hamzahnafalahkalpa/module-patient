<?php

namespace Hanafalah\ModulePatient\Resources\InternalReferral;

use App\Http\Resources\ApiResource;

class ShowInternalReferral extends ViewInternalReferral
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'referral'          => $this->relationValidation('referral', function () {
                return $this->referral->toShowApi();
            })
        ];
        $arr = array_merge(parent::toArray($request), $arr);

        return $arr;
    }
}
