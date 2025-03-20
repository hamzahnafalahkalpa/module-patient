<?php

namespace Zahzah\ModulePatient\Resources\PatientType;

use Zahzah\LaravelSupport\Resources\ApiResource;

class ViewPatientType extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'    => $this->id,
            "name"  => $this->name
        ];
        
        return $arr;
    }
}

