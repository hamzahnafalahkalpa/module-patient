<?php

namespace Hanafalah\ModulePatient\Resources\ExternalReferral;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewExternalReferral extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'                => $this->id,
            'date'              => $this->date,           
            'doctor_name'       => $this->doctor_name,                  
            'phone'             => $this->phone,            
            'facility_name'     => $this->facility_name,                    
            'unit_name'         => $this->unit_name,                
            'initial_diagnose'  => $this->initial_diagnose,                       
            'primary_diagnose'  => $this->primary_diagnose,                       
            'note'              => $this->note,           
        ];
        return $arr;
    }
}
