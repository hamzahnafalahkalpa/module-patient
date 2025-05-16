<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationData;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\VisitExamination
 * @method self conditionals(mixed $conditionals)
 * @method bool deleteVisitExamination()
 * @method bool prepareDeleteVisitExamination(? array $attributes = null)
 * @method mixed getVisitExamination()
 * @method ?Model prepareShowVisitExamination(?Model $model = null, ?array $attributes = null)
 * @method array showVisitExamination(?Model $model = null)
 * @method Collection prepareViewVisitExaminationList()
 * @method array viewVisitExaminationList()
 * @method LengthAwarePaginator prepareViewVisitExaminationPaginate(PaginateData $paginate_dto)
 * @method array viewVisitExaminationPaginate(?PaginateData $paginate_dto = null)
 * @method array storeVisitExamination(?VisitExaminationData $visit_examination_dto = null)
 * @method Builder visitExamination(mixed $conditionals = null)
 */
interface VisitExamination extends DataManagement {
    public function prepareStoreVisitExamination(VisitExaminationData $visit_examination_dto): Model;
}
