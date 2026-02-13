<?php

namespace Hanafalah\ModulePatient\Resources\VisitPatient;

class ShowVisitPatient extends ViewVisitPatient
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'patient'            => $this->relationValidation('patient', function () {
                return $this->patient->toShowApi()->resolve();
            },$this->prop_patient),
            'visit_registration'  => $this->relationValidation('visitRegistration',function(){
                return $this->visitRegistration->toShowApi()->resolve();                
            }),               
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
