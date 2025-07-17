<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Hanafalah\ModulePatient\Resources\PatientTypeService\{ViewPatientTypeService, ShowPatientTypeService};

class PatientTypeService extends PatientType
{
    protected $table = 'unicodes';

    public function getViewResource(){
        return ViewPatientTypeService::class;
    }

    public function getShowResource(){
        return ShowPatientTypeService::class;
    }
}
