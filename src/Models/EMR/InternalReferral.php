<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModulePatient\Resources\InternalReferral\ShowInternalReferral;
use Hanafalah\ModulePatient\Resources\InternalReferral\ViewInternalReferral;

class InternalReferral extends BaseModel
{
    use HasUlids, HasProps;

    //MEDIC SERVICE FLAG IN ENUM
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    protected $list = ['id', 'medic_service_id', 'props'];
    protected $show = [];

    public function toViewApi()
    {
        return new ViewInternalReferral($this);
    }

    public function getShowResource()
    {
        return new ShowInternalReferral($this);
    }

    public function referral()
    {
        return $this->morphOneModel('Referral', 'reference');
    }
    public function medicService()
    {
        return $this->belongsToModel("MedicService");
    }
}
