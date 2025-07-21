<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Hanafalah\ModuleMedicService\Models\MedicService;
use Hanafalah\ModulePatient\Resources\PatientType\ShowPatientType;
use Hanafalah\ModulePatient\Resources\PatientType\ViewPatientType;

class PatientType extends MedicService
{
    protected $table = 'unicodes';

    public function getViewResource(){
        return ViewPatientType::class;
    }

    public function getShowResource(){
        return ShowPatientType::class;
    }
}
