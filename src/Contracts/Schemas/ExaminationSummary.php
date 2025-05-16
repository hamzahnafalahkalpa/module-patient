<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\ExaminationSummaryData;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\ExaminationSummary
 * @method self conditionals(mixed $conditionals)
 * @method bool deleteExaminationSummary()
 * @method bool prepareDeleteExaminationSummary(? array $attributes = null)
 * @method mixed getExaminationSummary()
 * @method ?Model prepareShowExaminationSummary(?Model $model = null, ?array $attributes = null)
 * @method array showExaminationSummary(?Model $model = null)
 * @method Collection prepareViewExaminationSummaryList()
 * @method array viewExaminationSummaryList()
 * @method LengthAwarePaginator prepareViewExaminationSummaryPaginate(PaginateData $paginate_dto)
 * @method array viewExaminationSummaryPaginate(?PaginateData $paginate_dto = null)
 * @method array storeExaminationSummary(?ExaminationSummaryData $examination_summary_dto = null)
 * @method Builder examinationSummary(mixed $conditionals = null)
 */
interface ExaminationSummary extends DataManagement
{
    // public function prepareStoreExaminationSummary(ExaminationSummaryData $examination_summary_dto): Model;
}
