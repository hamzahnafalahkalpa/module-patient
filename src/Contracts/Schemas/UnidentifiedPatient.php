<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\ModulePatient\Contracts\Data\UnidentifiedPatientData;
//use Hanafalah\ModulePatient\Contracts\Data\UnidentifiedPatientUpdateData;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\UnidentifiedPatient
 * @method mixed export(string $type)
 * @method self conditionals(mixed $conditionals)
 * @method array updateUnidentifiedPatient(?UnidentifiedPatientData $unidentified_patient_dto = null)
 * @method Model prepareUpdateUnidentifiedPatient(UnidentifiedPatientData $unidentified_patient_dto)
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
 * @method Collection prepareStoreMultipleUnidentifiedPatient(array $datas)
 * @method array storeMultipleUnidentifiedPatient(array $datas)
 */

interface UnidentifiedPatient extends DataManagement
{
    public function prepareStoreUnidentifiedPatient(UnidentifiedPatientData $unidentified_patient_dto): Model;
}