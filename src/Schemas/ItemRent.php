<?php

namespace Hanafalah\ModulePatient\Schemas;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModulePatient\{
    Supports\BaseModulePatient
};
use Hanafalah\ModulePatient\Contracts\Schemas\ItemRent as ContractsItemRent;
use Hanafalah\ModulePatient\Contracts\Data\ItemRentData;

class ItemRent extends BaseModulePatient implements ContractsItemRent
{
    protected string $__entity = 'ItemRent';
    public $item_rent_model;
    //protected mixed $__order_by_created_at = false; //asc, desc, false

    protected array $__cache = [
        'index' => [
            'name'     => 'item_rent',
            'tags'     => ['item_rent', 'item_rent-index'],
            'duration' => 24 * 60
        ]
    ];

    public function prepareStoreItemRent(ItemRentData $item_rent_dto): Model{
        $add = [
            'reference_type' => $item_rent_dto->reference_type,
            'reference_id'   => $item_rent_dto->reference_id,
            'flag'           => $item_rent_dto->flag,
            'service_id'     => $item_rent_dto->service_id,
            'asset_type'     => $item_rent_dto->asset_type,
            'asset_id'       => $item_rent_dto->asset_id
        ];
        if (isset($item_rent_dto->id)){
            $guard  = ['id' => $item_rent_dto->id];
            $create = [$guard, $add];
        }else{
            $create = [$add];
        }
        $item_rent = $this->usingEntity()->updateOrCreate(...$create);

        $service = $this->ServiceModel()->findOrFail($item_rent_dto->service_id);
        $item_rent_dto->prop_service = $service->toViewApi()->resolve();
        if (isset($item_rent_dto->asset_type) && isset($item_rent_dto->asset_id)){
            $asset = $this->{$item_rent_dto->asset_type.'Model'}()
                                              ->findOrFail($item_rent_dto->asset_id);
            $item_rent_dto->prop_asset = $asset->toViewApi()->resolve();
            $this->schemaContract('inventory')->prepareUsedBy($asset,$item_rent_dto->reference_model);
        }
        $item_rent_dto->prop_asset = $service->toViewApi()->resolve();

        $this->fillingProps($item_rent,$item_rent_dto->props);
        $item_rent->save();
        return $this->item_rent_model = $item_rent;
    }
}