<?php

namespace Zahzah\ModulePatient\Concerns\Emr;

trait HasExaminationSummary
{
    public function examinationSummary()
    {
        return $this->morphOneModel('ExaminationSummary','reference');
    }
}