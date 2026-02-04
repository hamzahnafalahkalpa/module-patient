<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Hanafalah\ModulePatient\Resources\PatientTypeService\{ViewPatientTypeService, ShowPatientTypeService};

class PatientTypeService extends PatientType
{
    protected $table = 'unicodes';

    protected static function booted(): void{
        parent::booted();
        static::addGlobalScope('flag',function($query){
            $query->where('flag','PatientTypeService');
        });
        static::creating(function ($query) {
            $query->flag = 'PatientTypeService';
        });
    }

    public function getViewResource(){
        return ViewPatientTypeService::class;
    }

    public function getShowResource(){
        return ShowPatientTypeService::class;
    }
}
