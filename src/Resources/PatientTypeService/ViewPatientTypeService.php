<?php

namespace Hanafalah\ModulePatient\Resources\PatientTypeService;

use Hanafalah\ModulePatient\Resources\PatientType\ViewPatientType;

class ViewPatientTypeService extends ViewPatientType
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [];
        $arr = $this->mergeArray(parent::toArray($request),$arr);
        return $arr;
    }
}
