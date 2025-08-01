<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\ReferralData as DataReferralData;
use Hanafalah\ModulePatient\Contracts\Data\ReferralPropsData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class ReferralData extends Data implements DataReferralData{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('referral_code')]
    #[MapName('referral_code')]
    public ?string $referral_code = null;

    #[MapInputName('referral_type')]
    #[MapName('referral_type')]
    public ?string $referral_type = null;

    #[MapInputName('visit_type')]
    #[MapName('visit_type')]
    public ?string $visit_type = null;

    #[MapInputName('visit_id')]
    #[MapName('visit_id')]
    public mixed $visit_id = null;

    #[MapInputName('visit_model')]
    #[MapName('visit_model')]
    public ?object $visit_model = null;

    #[MapInputName('medic_service_id')]
    #[MapName('medic_service_id')]
    public mixed $medic_service_id = null;

    #[MapInputName('visit_registration')]
    #[MapName('visit_registration')]
    public ?VisitRegistrationData $visit_registration = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?ReferralPropsData $props = null;

    public static function before(array &$attributes){
        if (isset($attributes['visit_registration'])){
            $attributes['visit_registration']['medic_service_id'] ??= $attributes['medic_service_id'];
        }
    }

    public static function after(self $data): self{
        $new = static::new();
        $data->referral_type ??= 'INTERNAL';
        return $data;
    }
}