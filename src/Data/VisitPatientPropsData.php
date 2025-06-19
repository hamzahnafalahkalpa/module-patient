<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\VisitPatientPropsData as DataVisitPatientPropsData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class VisitPatientPropsData extends Data implements DataVisitPatientPropsData{
    #[MapInputName('prop_patient')]
    #[MapName('prop_patient')]
    public ?array $prop_patient = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;
}