<?php

namespace Hanafalah\ModulePatient\Resources\InternalReferral;

use App\Http\Resources\ApiResource;

class ViewInternalReferral extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'                => $this->id,
            'referral'          => $this->relationValidation('referral', function () {
                return $this->referral->toViewApi()->resolve();
            }),
            'medic_service'     => $this->relationValidation('medicService', function () {
                return $this->medicService->toViewApi()->resolve();
            })
        ];

        return $arr;
    }
}
