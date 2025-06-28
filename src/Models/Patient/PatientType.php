<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Hanafalah\LaravelSupport\Models\Unicode\Unicode;
use Hanafalah\ModulePatient\Resources\PatientType\ShowPatientType;
use Hanafalah\ModulePatient\Resources\PatientType\ViewPatientType;

class PatientType extends Unicode
{
    protected $table = 'unicodes';

    public function getViewResource(){
        return ViewPatientType::class;
    }

    public function getShowResource(){
        return ShowPatientType::class;
    }
}
