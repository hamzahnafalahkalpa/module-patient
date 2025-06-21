<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\ModulePatient\Contracts\Data\PatientOccupationData as DataPatientOccupationData;
use Hanafalah\ModuleProfession\Data\OccupationData;

class PatientOccupationData extends OccupationData implements DataPatientOccupationData{
    public static function before(array &$attributes){
        $attributes['flag'] ??= 'PatientOccupation';
        parent::before($attributes);
    }
}