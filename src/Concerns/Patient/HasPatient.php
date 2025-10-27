<?php

namespace Hanafalah\ModulePatient\Concerns\Patient;

trait HasPatient
{
    public function initializeHasPatient()
    {
        $this->mergeFillable([
            $this->getPatientForeignKey()
        ]);
    }

    protected function getPatientForeignKey()
    {
        return $this->PatientModel()->getForeignKey();
    }

    public function patient()
    {
        return $this->belongsToModel('Patient');
    }
}
