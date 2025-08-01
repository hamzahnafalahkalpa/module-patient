<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\ModulePatient\Resources\ItemRent\{
    ViewItemRent,
    ShowItemRent
};
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ItemRent extends BaseModel
{
    use HasUlids, HasProps, SoftDeletes;
    
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    public $list = [
        'id', 'reference_type', 'reference_id', 'flag',
        'service_id', 'asset_type', 'asset_id', 'props'
    ];

    protected $casts = [
        'asset_name' => 'string'
    ];

    protected static function booted(): void{
        parent::booted();
        static::addGlobalScope(function($query){
            $query->flagIn((new static)->getMorphClass());
        });
        static::creating(function($query){
            $query->flag ??= (new static)->getMorphClass();
        });
    }

    public function getPropsQuery(): array{
        return [
            'asset_name' => 'props->prop_asset->name'
        ];
    }

    public function viewUsingRelation(): array{
        return [];
    }

    public function showUsingRelation(): array{
        return [];
    }

    public function getViewResource(){
        return ViewItemRent::class;
    }

    public function getShowResource(){
        return ShowItemRent::class;
    }

    public function service(){return $this->belongsToModel('Service');}
    public function asset(){return $this->morphTo();}
}
