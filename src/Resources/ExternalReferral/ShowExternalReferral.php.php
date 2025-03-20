<?php

namespace Zahzah\ModulePatient\Resources\ExternalReferral;

use App\Http\Resources\ApiResource;

class ShowExternalReferral extends ViewExternalReferral
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'visit_patient'     => $this->relationValidation('visitPatient',function(){
                return $this->visitPatient->toShowApi();
            }),          
            'doctor_name'       => $this->doctor_name,
            'note'              => $this->note
        ];
        
        return $arr;
    }
}
