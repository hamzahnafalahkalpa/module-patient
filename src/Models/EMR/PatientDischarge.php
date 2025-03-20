<?php

namespace Zahzah\ModulePatient\Models\EMR;

use Zahzah\LaravelHasProps\Concerns\HasProps;
use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\ModulePatient\Enums\VisitPatient\VisitStatus;

class PatientDischarge extends BaseModel{
    use HasProps;

    //MEDIC SERVICE FLAG IN ENUM
    protected $list = ['id','visit_patient_id','discharge_status','props'];
    protected $show = []; 

    protected $casts = [
        'name' => 'string'
    ];

    public function getPropsQuery(): array{
        return [
            //FOR HEAD DOCTOR
            'name' => 'props->prop_people->name'
        ];
    }

    public function visitPatient(){return $this->belongsToModel('VisitPatient');}
    
}
