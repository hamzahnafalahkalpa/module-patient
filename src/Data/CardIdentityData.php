<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\CardIdentityData as DataCardIdentityData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class CardIdentityData extends Data implements DataCardIdentityData{
    #[MapInputName('medical_record')]
    #[MapName('medical_record')]
    public ?string $medical_record = null;
    
    #[MapInputName('old_medical_record')]
    #[MapName('old_medical_record')]
    public ?string $old_medical_record = null;

    #[MapInputName('ihs_number')]
    #[MapName('ihs_number')]
    public ?string $ihs_number = null;

    #[MapInputName('bpjs')]
    #[MapName('bpjs')]
    public ?string $bpjs = null;
}