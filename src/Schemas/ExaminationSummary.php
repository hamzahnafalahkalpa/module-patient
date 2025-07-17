<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModulePatient\Contracts\Schemas\ExaminationSummary as ContractsExaminationSummary;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModulePatient\Contracts\Data\ExaminationSummaryData;

class ExaminationSummary extends PackageManagement implements ContractsExaminationSummary
{
    protected string $__entity = 'ExaminationSummary';

    public function prepareStoreExaminationSummary(ExaminationSummaryData $examination_summary_dto): Model
    {
        if (isset($examination_summary_dto->id)) {
            $guard = ['id' => $examination_summary_dto->id];
        } else {
            if (!isset($examination_summary_dto->reference_type) || !isset($examination_summary_dto->reference_id)) {
                throw new \Exception('reference_type and reference_id is required', 422);
            }
            $guard = [
                'reference_type' => $examination_summary_dto->reference_type, 
                'reference_id'   => $examination_summary_dto->reference_id
            ];
        }

        $model = $this->usingEntity()->firstOrCreate($guard);
        $this->fillingProps($model, $examination_summary_dto);
        $model->save();
        return static::$examination_summary = $model;
    }
}
