<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModulePatient\Resources\PatientType\ShowPatientType;
use Hanafalah\ModulePatient\Resources\PatientType\ViewPatientType;
use Hanafalah\ModuleService\Concerns\HasService;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class PatientType extends BaseModel
{
    use HasUlids, SoftDeletes, HasProps, HasService;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    protected $list = ['id', 'parent_id', 'name', 'flag', 'label', 'props'];

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope('flag',function($query){
            $query->where('flag','IDENTITY');
        });
        static::creating(function ($query) {
            $query->flag ??= 'IDENTITY';
        });
        static::created(function ($query) {
            $parent    = $query->parent;
            $parent_id = null;
            if (isset($parent)) $parent_id = $parent->service->getKey();
            $query->service()->updateOrCreate([
                'parent_id' => $parent_id,
                'name'      => $query->name,
            ], [
                'status' => 'ACTIVE'
            ]);
        });
    }

    public function viewUsingRelation(): array{
        return ['service'];
    }

    public function showUsingRelation(): array{
        return ['service'];
    }

    public function getViewResource(){
        return ViewPatientType::class;
    }

    public function getShowResource(){
        return ShowPatientType::class;
    }
}
