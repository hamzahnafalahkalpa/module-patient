<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\ModulePatient\Resources\UnidentifiedPatient\{
    ViewUnidentifiedPatient,
    ShowUnidentifiedPatient
};
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class UnidentifiedPatient extends BaseModel
{
    use HasUlids, HasProps, SoftDeletes;
    
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    public $list = [
        'id',
        'name',
        'props',
    ];

    protected $casts = [
        'name' => 'string'
    ];


    public function viewUsingRelation(): array{
        return [];
    }

    public function showUsingRelation(): array{
        return [];
    }

    public function getViewResource(){
        return ViewUnidentifiedPatient::class;
    }

    public function getShowResource(){
        return ShowUnidentifiedPatient::class;
    }

    

    
}
