<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\InternalReferralData as DataInternalReferralData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class InternalReferralData extends Data implements DataInternalReferralData {
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id;
    
    #[MapInputName('medic')]
    #[MapName('medic')]
    public ?string $medic = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;
}