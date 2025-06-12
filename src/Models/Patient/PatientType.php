<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Hanafalah\ModuleMedicService\Models\MedicService;
use Hanafalah\ModulePatient\Resources\PatientType\ShowPatientType;
use Hanafalah\ModulePatient\Resources\PatientType\ViewPatientType;

class PatientType extends MedicService
{
    protected $table = 'medic_services';

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope('flag',function($query){
            $query->where('flag','IDENTITY');
        });
        static::creating(function ($query) {
            $query->flag = 'IDENTITY';
        });
    }

    public function getViewResource(){
        return ViewPatientType::class;
    }

    public function getShowResource(){
        return ShowPatientType::class;
    }
}
