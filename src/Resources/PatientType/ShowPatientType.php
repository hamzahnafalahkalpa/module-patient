<?php

namespace Hanafalah\ModulePatient\Resources\PatientType;

class ShowPatientType extends ViewPatientType
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [];
        $arr = array_merge(parent::toArray($request), $arr);
        return $arr;
    }
}
