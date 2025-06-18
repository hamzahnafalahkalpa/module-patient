<?php

namespace Hanafalah\ModulePatient\Resources\PatientTypeService;

use Hanafalah\ModulePatient\Resources\PatientType\ShowPatientType;

class ShowPatientTypeService extends ViewPatientTypeService
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [];
        $show = $this->resolveNow(new ShowPatientType($this));
        $arr = $this->mergeArray(parent::toArray($request), $show, $arr);
        return $arr;
    }
}
