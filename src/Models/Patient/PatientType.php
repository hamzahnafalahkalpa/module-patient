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

    public function getViewResource()
    {
        return ViewPatientType::class;
    }

    public function getShowResource()
    {
        return ShowPatientType::class;
    }

    //EIGER SECCTION

    public function service()
    {
        return $this->hasOneModel('service');
    }
    //END EIGER SECTION
}
