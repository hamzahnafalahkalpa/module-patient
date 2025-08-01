<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\ModulePatient\Contracts\Data\ItemRentData;
//use Hanafalah\ModulePatient\Contracts\Data\ItemRentUpdateData;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\ItemRent
 * @method mixed export(string $type)
 * @method self conditionals(mixed $conditionals)
 * @method array updateItemRent(?ItemRentData $item_rent_dto = null)
 * @method Model prepareUpdateItemRent(ItemRentData $item_rent_dto)
 * @method bool deleteItemRent()
 * @method bool prepareDeleteItemRent(? array $attributes = null)
 * @method mixed getItemRent()
 * @method ?Model prepareShowItemRent(?Model $model = null, ?array $attributes = null)
 * @method array showItemRent(?Model $model = null)
 * @method Collection prepareViewItemRentList()
 * @method array viewItemRentList()
 * @method LengthAwarePaginator prepareViewItemRentPaginate(PaginateData $paginate_dto)
 * @method array viewItemRentPaginate(?PaginateData $paginate_dto = null)
 * @method array storeItemRent(?ItemRentData $item_rent_dto = null)
 * @method Collection prepareStoreMultipleItemRent(array $datas)
 * @method array storeMultipleItemRent(array $datas)
 */

interface ItemRent extends DataManagement
{
    public function prepareStoreItemRent(ItemRentData $item_rent_dto): Model;
}