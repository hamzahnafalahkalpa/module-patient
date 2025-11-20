<?php

namespace Hanafalah\ModulePatient\Resources\VisitPatient;

use Hanafalah\ModuleTransaction\Resources\Transaction\ShowTransaction;

class ShowVisitPatient extends ViewVisitPatient
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'patient'            => $this->relationValidation('patient', function () {
                return $this->patient->toShowApi()->resolve();
            },$this->prop_patient),
            'transaction'         => $this->relationValidation('reference',function(){
                return $this->reference->toShowApi()->resolve();
            },$this->propNil($this->prop_transaction,'reference')),
            "visit_registrations" => $this->relationValidation("visitRegistrations", function () {
                return $this->visitRegistrations->transform(function ($visitRegistration) {
                    return is_array($visitRegistration) 
                            ? $this->propNil($visitRegistration,'visit_patient')
                            : $visitRegistration->toShowApiExcepts('visit_patient');
                });
            }),
            "family_relationship" => $this->relationValidation("familyRelationship", function () {
                return $this->familyRelationship->toViewApi()->resolve();
            },$this->prop_family_relationship),
            'transaction'   => $this->relationValidation('transaction', function () {
                return $this->transaction->toShowApi()->resolve();
            },$this->prop_transaction),
            "organizations" => $this->relationValidation("organizations", function () {
                return $this->organizations->transform(function ($organization) {
                    return $organization->toViewApi()->resolve();
                });
            })
        ];
        $arr = array_merge(parent::toArray($request), $arr);

        return $arr;
    }
}
