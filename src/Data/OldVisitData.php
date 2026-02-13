<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\OldVisitData as DataOldVisitData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class OldVisitData extends Data implements DataOldVisitData{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('patient_id')]
    #[MapName('patient_id')]
    public mixed $patient_id = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;
}