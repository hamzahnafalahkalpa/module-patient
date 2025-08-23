<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\PractitionerEvaluationData;

/**
 * @see \Hanafalah\ModulePatient\Schemas\PractitionerEvaluation
 * @method self setParamLogic(string $logic, bool $search_value = false, ?array $optionals = [])
 * @method self conditionals(mixed $conditionals)
 * @method bool deletePractitionerEvaluation()
 * @method bool prepareDeletePractitionerEvaluation(? array $attributes = null)
 * @method mixed getPractitionerEvaluation()
 * @method ?Model prepareShowPractitionerEvaluation(?Model $model = null, ?array $attributes = null)
 * @method array showPractitionerEvaluation(?Model $model = null)
 * @method Collection prepareViewPractitionerEvaluationList()
 * @method array viewPractitionerEvaluationList()
 * @method LengthAwarePaginator prepareViewPractitionerEvaluationPaginate(PaginateData $paginate_dto)
 * @method array viewPractitionerEvaluationPaginate(?PaginateData $paginate_dto = null)
 * @method array storePractitionerEvaluation(?PractitionerEvaluationData $practitioner_evaluation_dto = null)
 * @method Builder practitionerEvaluation(mixed $conditionals = null)
 */
interface PractitionerEvaluation extends DataManagement
{
    public function prepareStorePractitionerEvaluation(PractitionerEvaluationData $practitioner_evaluation_dto): Model;
}
