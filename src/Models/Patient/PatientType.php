<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Hanafalah\LaravelSupport\Models\Unicode\Unicode;
use Hanafalah\ModulePatient\Resources\PatientType\ShowPatientType;
use Hanafalah\ModulePatient\Resources\PatientType\ViewPatientType;

class PatientType extends Unicode
{
    protected $table = 'unicodes';

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
