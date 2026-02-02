<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModulePatient\Contracts\Schemas\ExaminationSummary as ContractsExaminationSummary;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModuleExamination\Schemas\PatientSummary;
use Hanafalah\ModulePatient\Contracts\Data\ExaminationSummaryData;

class ExaminationSummary extends PatientSummary implements ContractsExaminationSummary
{
    protected string $__entity = 'ExaminationSummary';
    public $examination_summary_model;

    public function prepareStoreExaminationSummary(mixed $examination_summary_dto): Model
    {
        $add = [
            'patient_id' => $examination_summary_dto->patient_id,            
        ];
        if (!isset($examination_summary_dto->id) && !isset($examination_summary_dto->patient_id)){
            $guard = [
                'reference_type' => $examination_summary_dto->reference_type,
                'reference_id'   => $examination_summary_dto->reference_id
            ];
            $create = [$guard,$add];
        }else{
            $add = array_merge($add,[
                'reference_type' => $examination_summary_dto->reference_type,
                'reference_id'   => $examination_summary_dto->reference_id
            ]);
            $create = [$add];
        }
        $examination_summary = $this->usingEntity()->updateOrCreate(...$create);

        $patient_model = $examination_summary_dto->patient_model ??= $this->PatientModel()->findOrFail($examination_summary_dto->patient_id);
        $examination_summary_dto->props['prop_patient'] = $patient_model->toShowApi()->resolve();

        $this->setEmrData($examination_summary_dto, $examination_summary);
        if (isset($examination_summary_dto->props['test']) && $examination_summary_dto->props['test']){
            $last_visit = $examination_summary_dto->props['last_visit'];
            unset($last_visit['visit_registration'],$last_visit['visit_patient']);
            $examination_summary_dto->props['last_visit'] = $last_visit;
        }
        $this->fillingProps($examination_summary, $examination_summary_dto->props);
        $examination_summary->save();
        return $this->examination_summary_model = $examination_summary;
    }
}
