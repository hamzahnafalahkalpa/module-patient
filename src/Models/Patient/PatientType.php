<?php

namespace Zahzah\ModulePatient\Models\Patient;

use Illuminate\Database\Eloquent\SoftDeletes;
use Zahzah\LaravelHasProps\Concerns\HasProps;
use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\ModulePatient\Resources\PatientType\ShowPatientType;
use Zahzah\ModulePatient\Resources\PatientType\ViewPatientType;

class PatientType extends BaseModel{
    use SoftDeletes, HasProps;

    protected $list = ['id','name','props'];

    public function toViewApi(){
        return new ViewPatientType($this);
    }

    public function toShowApi(){
        return new ShowPatientType($this);
    }

    //EIGER SECCTION

    public function service(){return $this->hasOneModel('service');}
    //END EIGER SECTION
}