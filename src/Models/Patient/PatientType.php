<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModulePatient\Resources\PatientType\ShowPatientType;
use Hanafalah\ModulePatient\Resources\PatientType\ViewPatientType;

class PatientType extends BaseModel
{
    use SoftDeletes, HasProps;

    protected $list = ['id', 'name', 'props'];

    public function toViewApi()
    {
        return new ViewPatientType($this);
    }

    public function getShowResource()
    {
        return new ShowPatientType($this);
    }

    //EIGER SECCTION

    public function service()
    {
        return $this->hasOneModel('service');
    }
    //END EIGER SECTION
}
