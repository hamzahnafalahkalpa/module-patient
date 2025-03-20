<?php

namespace Hanafalah\ModulePatient\Resources\VisitPatient;

use Hanafalah\ModuleTransaction\Resources\Transaction\ShowTransaction;

class ShowVisitPatient extends ViewVisitPatient
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'transaction'   => $this->relationValidation('transaction', function () {
                return $this->transaction->toShowApi();
            }),
            "organizations" => $this->relationValidation("organizations", function () {
                return $this->organizations->transform(function ($organization) {
                    return $organization->toViewApi();
                });
            }),
            "payer"       => $this->relationValidation("payer", function () {
                return $this->payer->toShowApi();
            }),
            "agent"       => $this->relationValidation("agent", function () {
                return $this->agent->toShowApi();
            }),
        ];
        $arr = array_merge(parent::toArray($request), $arr);

        return $arr;
    }
}
