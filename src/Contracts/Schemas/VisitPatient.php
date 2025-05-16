<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\VisitPatientData;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\VisitPatient
 * @method self conditionals(mixed $conditionals)
 * @method bool deleteVisitPatient()
 * @method mixed getVisitPatient()
 * @method ?Model prepareShowVisitPatient(?Model $model = null, ?array $attributes = null)
 * @method array showVisitPatient(?Model $model = null)
 * @method Collection prepareViewVisitPatientList()
 * @method array viewVisitPatientList()
 * @method LengthAwarePaginator prepareViewVisitPatientPaginate(PaginateData $paginate_dto)
 * @method array viewVisitPatientPaginate(?PaginateData $paginate_dto = null)
 * @method array storeVisitPatient(?VisitPatientData $visit_patient_dto = null)
 * @method Builder visitPatient(mixed $conditionals = null)
 */
interface VisitPatient extends DataManagement {
    public function prepareStoreVisitPatient(VisitPatientData $visit_patient_dto): Model;
    public function prepareDeleteVisitPatient(? array $attributes = null): mixed;
}
