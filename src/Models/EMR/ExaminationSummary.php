<?php

namespace Zahzah\ModulePatient\Models\EMR;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Zahzah\LaravelHasProps\Concerns\HasProps;
use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\ModulePatient\Resources\ExaminationSummary\{
    ShowExaminationSummary,
    ViewExaminationSummary
};

class ExaminationSummary extends BaseModel {
    use HasUlids, HasProps;

    public $incrementing  = false;
    protected $keyType    = "string";
    protected $primaryKey = 'id';
    protected $list       = ['id','reference_type','reference_id','group_summary_id','props'];
    protected $show       = ['parent_id'];

    public function reference(){return $this->morphTo();}
    public function group(){return $this->belongsToModel('ExaminationSummary','group_summary_id');}

    public function toViewApi()
    {
        return new ViewExaminationSummary($this);
    }

    public function toShowApi(){
        return new ShowExaminationSummary($this);
    }
}
