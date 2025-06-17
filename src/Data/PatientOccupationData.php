<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\ModulePatient\Contracts\Data\PatientOccupationData as DataPatientOccupationData;
use Hanafalah\ModuleProfession\Data\OccupationData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class PatientOccupationData extends OccupationData implements DataPatientOccupationData{
    #[MapInputName('flag')]
    #[MapName('flag')]
    public ?string $flag = 'PatientOccupation';

    public static function before(array &$attributes){
        $attributes['flag'] = 'PatientOccupation';
    }
}