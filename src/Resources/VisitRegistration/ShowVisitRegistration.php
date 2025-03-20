<?php

namespace Zahzah\ModulePatient\Resources\VisitRegistration;

class ShowVisitRegistration extends ViewVisitRegistration
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'services'            => $this->relationValidation('services',function(){
                return $this->services->transform(function($service){
                    return $service->toViewApi();
                });
            }),
            'visit_registrations' => $this->relationValidation('visitRegistrations',function(){
                return $this->visitRegistrations->transform(function($visitRegistration){
                    return $visitRegistration->toShowApi();
                });
            })
        ];
        $arr = array_merge(parent::toArray($request), $arr);
        
        return $arr;
    }
}
