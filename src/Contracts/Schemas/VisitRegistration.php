<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\VisitRegistrationData;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\VisitRegistration
 * @method self setParamLogic(string $logic, bool $search_value = false, ?array $optionals = [])
 * @method self conditionals(mixed $conditionals)
 * @method bool deleteVisitRegistration()
 * @method array updateVisitRegistration(?VisitRegistrationData $update_visit_registration_dto = null)
 * @method Model prepareUpdateVisitRegistration(UpdateVisitRegistrationData $update_visit_registration_dto)
 * @method bool prepareDeleteVisitRegistration(? array $attributes = null)
 * @method mixed getVisitRegistration()
 * @method ?Model prepareShowVisitRegistration(?Model $model = null, ?array $attributes = null)
 * @method array showVisitRegistration(?Model $model = null)
 * @method Collection prepareViewVisitRegistrationList()
 * @method array viewVisitRegistrationList()
 * @method LengthAwarePaginator prepareViewVisitRegistrationPaginate(PaginateData $paginate_dto)
 * @method array viewVisitRegistrationPaginate(?PaginateData $paginate_dto = null)
 * @method array storeVisitRegistration(?VisitRegistrationData $visit_registration_dto = null)
 * @method Builder visitRegistration(mixed $conditionals = null)
 */
interface VisitRegistration extends DataManagement {
    public function prepareStoreVisitRegistration(VisitRegistrationData $visit_registration_dto): Model;
}
