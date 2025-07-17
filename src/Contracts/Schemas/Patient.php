<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\PatientData;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\Patient
 * @method self setParamLogic(string $logic, bool $search_value = false, ?array $optionals = [])
 * @method self conditionals(mixed $conditionals)
 * @method bool deletePatient()
 * @method bool prepareDeletePatient(? array $attributes = null)
 * @method mixed getPatient()
 * @method ?Model prepareShowPatient(?Model $model = null, ?array $attributes = null)
 * @method array showPatient(?Model $model = null)
 * @method Collection prepareViewPatientList()
 * @method array viewPatientList()
 * @method LengthAwarePaginator prepareViewPatientPaginate(PaginateData $paginate_dto)
 * @method array viewPatientPaginate(?PaginateData $paginate_dto = null)
 * @method array storePatient(?PatientData $patient_dto = null)
 * @method Builder patient(mixed $conditionals = null)
 */
interface Patient extends DataManagement {
    public function prepareStorePatient(PatientData $patient_dto): Model;
}
