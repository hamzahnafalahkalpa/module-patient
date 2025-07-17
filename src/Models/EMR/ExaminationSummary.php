<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModulePatient\Resources\ExaminationSummary\{
    ShowExaminationSummary,
    ViewExaminationSummary
};

class ExaminationSummary extends BaseModel
{
    use HasUlids, HasProps;

    public $incrementing  = false;
    protected $keyType    = "string";
    protected $primaryKey = 'id';
    protected $list       = ['id', 'reference_type', 'reference_id', 'group_summary_id', 'props'];
    protected $show       = ['parent_id'];

    public function reference()
    {
        return $this->morphTo();
    }
    public function group()
    {
        return $this->belongsToModel('ExaminationSummary', 'group_summary_id');
    }

    public function getViewResource()
    {
        return ViewExaminationSummary::class;
    }

    public function getShowResource()
    {
        return ShowExaminationSummary::class;
    }
}
