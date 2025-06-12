<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Hanafalah\ModulePatient\Resources\PatientTypeService\{ViewPatientTypeService, ShowPatientTypeService};

class PatientTypeService extends PatientType
{
    protected $table = 'medic_services';

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
