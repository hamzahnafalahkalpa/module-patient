<?php

namespace Zahzah\ModulePatient\Resources\PatientTypeHistory;

class ShowPatientTypeHistory extends ViewPatientTypeHistory
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [];
        $arr = array_merge(parent::toArray($request),$arr);
        
        return $arr;
    }
}

