<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\ModulePatient\Contracts\Data\PatientOccupationData;
use Hanafalah\ModuleProfession\Contracts\Schemas\Occupation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\PatientOccupation
 * @method self conditionals(mixed $conditionals)
 * @method bool deletePatientOccupation()
 * @method bool prepareDeletePatientOccupation(? array $attributes = null)
 * @method mixed getPatientOccupation()
 * @method ?Model prepareShowPatientOccupation(?Model $model = null, ?array $attributes = null)
 * @method array showPatientOccupation(?Model $model = null)
 * @method Collection prepareViewPatientOccupationList()
 * @method array viewPatientOccupationList()
 * @method LengthAwarePaginator prepareViewPatientOccupationPaginate(PaginateData $paginate_dto)
 * @method array viewPatientOccupationPaginate(?PaginateData $paginate_dto = null)
 * @method array storePatientOccupation(?PatientOccupationData $occupation_dto = null)s
 */
interface PatientOccupation extends Occupation
{
    public function prepareStorePatientOccupation(PatientOccupationData $occupation_dto): Model;
    public function patientOccupation(mixed $conditionals = null): Builder;
}
