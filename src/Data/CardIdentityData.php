<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\CardIdentityData as DataCardIdentityData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class CardIdentityData extends Data implements DataCardIdentityData{
    #[MapInputName('old_mr')]
    #[MapName('old_mr')]
    public ?string $old_mr = null;

    #[MapInputName('ihs_number')]
    #[MapName('ihs_number')]
    public ?string $ihs_number = null;

    #[MapInputName('bpjs')]
    #[MapName('bpjs')]
    public ?string $bpjs = null;
}