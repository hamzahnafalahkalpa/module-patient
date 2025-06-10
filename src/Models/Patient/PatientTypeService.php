<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Hanafalah\ModulePatient\Resources\PatientTypeService\{ViewPatientTypeService, ShowPatientTypeService};
use Hanafalah\ModulePatient\Models\Patient\PatientType;

class PatientTypeService extends PatientType
{
    protected $table = 'patient_types';

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope('flag',function($query){
            $query->where('flag','SERVICE');
        });
        static::creating(function ($query) {
            $query->flag = 'SERVICE';
        });
    }

    public function getViewResource(){
        return ViewPatientTypeService::class;
    }

    public function getShowResource(){
        return ShowPatientTypeService::class;
    }
}
