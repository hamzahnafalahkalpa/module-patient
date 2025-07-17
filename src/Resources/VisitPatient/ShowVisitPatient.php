<?php

namespace Hanafalah\ModulePatient\Resources\VisitPatient;

use Hanafalah\ModuleTransaction\Resources\Transaction\ShowTransaction;

class ShowVisitPatient extends ViewVisitPatient
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'transaction'   => $this->relationValidation('transaction', function () {
                return $this->transaction->toShowApi()->resolve();
            }),
            "organizations" => $this->relationValidation("organizations", function () {
                return $this->organizations->transform(function ($organization) {
                    return $organization->toViewApi()->resolve();
                });
            }),
            "payer"       => $this->relationValidation("payer", function () {
                return $this->payer->toShowApi()->resolve();
            }),
            "agent"       => $this->relationValidation("agent", function () {
                return $this->agent->toShowApi()->resolve();
            }),
        ];
        $arr = array_merge(parent::toArray($request), $arr);

        return $arr;
    }
}
