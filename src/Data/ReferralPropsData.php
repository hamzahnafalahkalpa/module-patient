<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\ExternalReferralData;
use Hanafalah\ModulePatient\Contracts\Data\ReferralPropsData as DataReferralPropsData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class ReferralPropsData extends Data implements DataReferralPropsData{
    #[MapInputName('external_referral')]
    #[MapName('external_referral')]
    public ?ExternalReferralData $external_referral = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;
}