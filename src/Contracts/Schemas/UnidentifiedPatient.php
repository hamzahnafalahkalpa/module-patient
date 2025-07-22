<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\PatientData;
use Hanafalah\ModulePatient\Contracts\Data\UnidentifiedPatientData;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\UnidentifiedPatient
 * @method self setParamLogic(string $logic, bool $search_value = false, ?array $optionals = [])
 * @method self conditionals(mixed $conditionals)
 * @method bool deleteUnidentifiedPatient()
 * @method bool prepareDeleteUnidentifiedPatient(? array $attributes = null)
 * @method mixed getUnidentifiedPatient()
 * @method ?Model prepareShowUnidentifiedPatient(?Model $model = null, ?array $attributes = null)
 * @method array showUnidentifiedPatient(?Model $model = null)
 * @method Collection prepareViewUnidentifiedPatientList()
 * @method array viewUnidentifiedPatientList()
 * @method LengthAwarePaginator prepareViewUnidentifiedPatientPaginate(PaginateData $paginate_dto)
 * @method array viewUnidentifiedPatientPaginate(?PaginateData $paginate_dto = null)
 * @method array storeUnidentifiedPatient(?UnidentifiedPatientData $unidentified_patient_dto = null)
 * @method Builder patient(mixed $conditionals = null)
 */
interface UnidentifiedPatient extends DataManagement {
    public function prepareStoreUnidentifiedPatient(UnidentifiedPatientData $unidentified_patient_dto): Model;
}
