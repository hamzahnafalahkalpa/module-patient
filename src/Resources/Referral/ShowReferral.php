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
            'visit' => $this->relationValidation('visit', function () {
                return $this->visit->toShowApi()->resolve();
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);
        return $arr;
    }
}
