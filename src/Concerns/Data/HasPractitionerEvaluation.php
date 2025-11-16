<?php

namespace Hanafalah\ModulePatient\Concerns\Data;

trait HasPractitionerEvaluation
{
    public function setupPractitionerEvaluation(array &$attributes){
        if (isset($attributes['practitioner_evaluation'])){
            $attributes['practitioner_evaluations'] ??= [];
            $attributes['practitioner_evaluation']['as_pic'] = true;
            $attributes['practitioner_evaluation']['role_as'] = 'DPJP';
            $attributes['practitioner_evaluations'][] = $attributes['practitioner_evaluation'];
        }
        if (isset($attributes['practitioner_evaluations']) && is_array($attributes['practitioner_evaluations'])){
            foreach ($attributes['practitioner_evaluations'] as &$practitioner_evaluation){
                $this->setPractitionerEvaluation($practitioner_evaluation);
            }
        }
    }

    private function setPractitionerEvaluation(array &$practitioner_evaluation){
        $practitioner_evaluation['practitioner_type'] ??= config('module-patient.practitioner');   

        $practitioner_model = app(config('database.models.'.$practitioner_evaluation['practitioner_type']));
        if (isset($practitioner_evaluation['practitioner_id'])){
            $practitioner_model = $practitioner_model->findOrFail($practitioner_evaluation['practitioner_id']);
        }
        $practitioner_evaluation['prop_practitioner'] = $practitioner_model->toViewApi()->resolve();
    }
}
