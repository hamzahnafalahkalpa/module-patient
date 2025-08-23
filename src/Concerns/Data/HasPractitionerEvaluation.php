<?php

namespace Hanafalah\ModulePatient\Concerns\Data;

trait HasPractitionerEvaluation
{
    public function setupPractitionerEvaluation(array &$attributes){
        $attributes['practitioner_evaluation'] ??= [];
        $practitioner_evaluation = &$attributes['practitioner_evaluation'];
        $practitioner_evaluation['practitioner_type'] ??= config('module-patient.practitioner');   
        $practitioner_model = app(config('database.models.'.$practitioner_evaluation['practitioner_type']));
        if (isset($practitioner_evaluation['practitioner_id'])){
            $practitioner_model = $practitioner_model->findOrFail($practitioner_evaluation['practitioner_id']);
        }
        $practitioner_evaluation['prop_practitioner'] = $practitioner_model->toViewApi()->resolve();
    }
}
