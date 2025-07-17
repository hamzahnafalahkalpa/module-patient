<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\VisitRegistrationPropsData as DataVisitRegistrationPropsData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class VisitRegistrationPropsData extends Data implements DataVisitRegistrationPropsData{
    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;
}