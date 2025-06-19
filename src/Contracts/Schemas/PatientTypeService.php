<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\PatientTypeServiceData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\PatientTypeService
 * @method self setParamLogic(string $logic, bool $search_value = false, ?array $optionals = [])
 * @method self conditionals(mixed $conditionals)
 * @method bool deletePatientTypeService()
 * @method bool prepareDeletePatientTypeService(? array $attributes = null)
 * @method mixed getPatientTypeService()
 * @method ?Model prepareShowPatientTypeService(?Model $model = null, ?array $attributes = null)
 * @method array showPatientTypeService(?Model $model = null)
 * @method Collection prepareViewPatientTypeServiceList()
 * @method array viewPatientTypeServiceList()
 * @method LengthAwarePaginator prepareViewPatientTypeServicePaginate(PaginateData $paginate_dto)
 * @method array viewPatientTypeServicePaginate(?PaginateData $paginate_dto = null)
 * @method array storePatientTypeService(?PatientTypeServiceData $patient_type_dto = null)
 * @method Builder patientTypeService(mixed $conditionals = null)
 */
interface PatientTypeService extends PatientType {
    public function prepareStorePatientTypeService(PatientTypeServiceData $patient_type_dto): Model;
}
