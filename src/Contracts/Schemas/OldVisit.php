<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\OldVisitData;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\OldVisit
 * @method self setParamLogic(string $logic, bool $search_value = false, ?array $optionals = [])
 * @method self conditionals(mixed $conditionals)
 * @method bool deleteOldVisit()
 * @method mixed getOldVisit()
 * @method ?Model prepareShowOldVisit(?Model $model = null, ?array $attributes = null)
 * @method array showOldVisit(?Model $model = null)
 * @method Collection prepareViewOldVisitList()
 * @method array viewOldVisitList()
 * @method LengthAwarePaginator prepareViewOldVisitPaginate(PaginateData $paginate_dto)
 * @method array viewOldVisitPaginate(?PaginateData $paginate_dto = null)
 * @method array storeOldVisit(?OldVisitData $old_visit_dto = null)
 * @method Builder oldVisit(mixed $conditionals = null)
 */
interface OldVisit extends DataManagement {
    public function prepareStoreOldVisit(OldVisitData $old_visit_dto): Model;
}
