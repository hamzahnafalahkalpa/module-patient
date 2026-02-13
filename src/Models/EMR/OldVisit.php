<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModulePatient\Resources\OldVisit\ViewOldVisit;

class OldVisit extends BaseModel
{
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    public $list = [
        'id', 'patient_id', 'props'
    ];

    public function getViewResource(){
        return ViewOldVisit::class;
    }

    public function getShowResource(){
        return ViewOldVisit::class;
    }

    public function patient() {return $this->belongsToModel('Patient');}
}
