<?php

namespace Hanafalah\ModulePatient\Concerns;

trait HasPractitionerEvaluation
{
    public function practitionerEvaluation(){return $this->morphOneModel('PractitionerEvaluation','reference');}
    public function practitionerEvaluations(){return $this->morphManyModel('PractitionerEvaluation','reference');}
}
