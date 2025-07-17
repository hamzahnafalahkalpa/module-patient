<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Data\UnicodeData;
use Hanafalah\ModulePatient\Contracts\Data\PatientTypeServiceData as DataPatientTypeServiceData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class PatientTypeServiceData extends UnicodeData implements DataPatientTypeServiceData{
    #[MapInputName('label')]
    #[MapName('label')]
    public ?string $label = 'UMUM';
    
    public static function before(array &$attributes){
        $attributes['flag'] ??= 'PatientTypeService';
        parent::before($attributes);
    }
}