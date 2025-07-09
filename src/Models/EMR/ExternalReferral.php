<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModulePatient\Resources\ExternalReferral\{ViewExternalReferral, ShowExternalReferral};

class ExternalReferral extends BaseModel
{
    use HasUlids, SoftDeletes, HasProps;

    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $list = [
        'id',
        'date',
        'doctor_name',
        'phone',
        'facility_name',
        'unit_name',
        'initial_diagnose',
        'primary_diagnose',
        'note',
        'props'
    ];

    public function getViewResource(){return ViewExternalReferral::class;}
    public function getShowResource(){return ShowExternalReferral::class;}
}
