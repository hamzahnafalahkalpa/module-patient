<?php

namespace Zahzah\ModulePatient\Models\EMR;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Zahzah\LaravelHasProps\Concerns\HasProps;
use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\ModulePatient\Resources\ExternalReferral\ShowExternalReferral;
use Zahzah\ModulePatient\Resources\ExternalReferral\ViewExternalReferral;

class ExternalReferral extends BaseModel{
    use HasUlids, SoftDeletes;

    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $list = [
        'id',"visit_patient_id", "date", "doctor_name", "phone", "facility_name",
        "unit_name", "initial_diagnose", "note"
    ];

    public function toViewApi(){
        return new ViewExternalReferral($this);
    }

    public function toShowApi(){
        return new ShowExternalReferral($this);
    }

    //EIGER SECCTION
    public function visitPatient(){return $this->belongsToModel('VisitPatient');}
}
