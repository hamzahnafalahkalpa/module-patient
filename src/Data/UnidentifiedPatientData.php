<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\UnidentifiedPatientData as DataUnidentifiedPatientData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class UnidentifiedPatientData extends Data implements DataUnidentifiedPatientData
{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('name')]
    #[MapName('name')]
    public ?string $name = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?UnidentifiedPropsPatientData $props = null;

    public static function before(array &$attributes){
        $attributes['sex'] ??= 'Male';
        $attributes['name'] ??= $attributes['sex'] == 'Male' ? 'John Doe' : 'Jane Doe';
    }
}