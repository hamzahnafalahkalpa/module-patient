<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModulePatient\Resources\ExternalReferral\ShowExternalReferral;
use Hanafalah\ModulePatient\Resources\ExternalReferral\ViewExternalReferral;

class ExternalReferral extends BaseModel
{
    use HasUlids, SoftDeletes;

    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $list = [
        'id',
        "visit_patient_id",
        "date",
        "doctor_name",
        "phone",
        "facility_name",
        "unit_name",
        "initial_diagnose",
        "note"
    ];

    public function getViewResource()
    {
        return ViewExternalReferral::class;
    }

    public function getShowResource()
    {
        return ShowExternalReferral::class;
    }

    //EIGER SECCTION
    public function visitPatient()
    {
        return $this->belongsToModel('VisitPatient');
    }
}
