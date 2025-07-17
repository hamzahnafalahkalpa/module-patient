<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\ModuleMedicService\Contracts\Schemas\MedicService;
use Hanafalah\ModulePatient\Contracts\Data\PatientTypeData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\PatientType
 * @method self setParamLogic(string $logic, bool $search_value = false, ?array $optionals = [])
 * @method self conditionals(mixed $conditionals)
 * @method bool deletePatientType()
 * @method bool prepareDeletePatientType(? array $attributes = null)
 * @method mixed getPatientType()
 * @method ?Model prepareShowPatientType(?Model $model = null, ?array $attributes = null)
 * @method array showPatientType(?Model $model = null)
 * @method Collection prepareViewPatientTypeList()
 * @method array viewPatientTypeList()
 * @method LengthAwarePaginator prepareViewPatientTypePaginate(PaginateData $paginate_dto)
 * @method array viewPatientTypePaginate(?PaginateData $paginate_dto = null)
 * @method array storePatientType(?PatientTypeData $patient_type_dto = null)
 */
interface PatientType extends MedicService {
    public function prepareStorePatientType(PatientTypeData $patient_type_dto): Model;
    public function patientType(mixed $conditionals = null): Builder;
}
