<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\ModulePatient\Contracts\Data\PatientTypeServiceData as DataPatientTypeServiceData;

class PatientTypeServiceData extends PatientTypeData implements DataPatientTypeServiceData{
    public static function after(mixed $data): PatientTypeServiceData{
        $data->flag = 'SERVICE';
        return $data;
    }
}