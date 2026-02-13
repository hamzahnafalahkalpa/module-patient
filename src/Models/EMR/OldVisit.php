<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModulePatient\Resources\OldVisit\ViewOldVisit;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;

class OldVisit extends BaseModel
{
    use HasUlids, HasProps, SoftDeletes;

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
