<?php

namespace Zahzah\ModulePatient\Resources\Referral;

class ShowReferral extends ViewReferral
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'reference'             => $this->relationValidation('reference',function(){
                return $this->reference->toShowApi();
            }),
            'visit_registration' => $this->relationValidation('visitRegistration',function(){
                return $this->visitRegistration->toShowApi();
            })
        ];

        $arr = array_merge(parent::toArray($request),$arr);
        
        return $arr;
    }
}
