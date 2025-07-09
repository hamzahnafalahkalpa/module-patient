<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\ReferralData as DataReferralData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class ReferralData extends Data implements DataReferralData{
    use HasRequestData;

    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('referral_code')]
    #[MapName('referral_code')]
    public ?string $referral_code = null;

    #[MapInputName('reference_type')]
    #[MapName('reference_type')]
    public ?string $reference_type = null;

    #[MapInputName('reference_id')]
    #[MapName('reference_id')]
    public mixed $reference_id = null;

    #[MapInputName('reference')]
    #[MapName('reference')]
    public null|array|object $reference = null;

    #[MapInputName('visit_type')]
    #[MapName('visit_type')]
    public ?string $visit_type = null;

    #[MapInputName('visit_id')]
    #[MapName('visit_id')]
    public mixed $visit_id = null;

    #[MapInputName('visit_model')]
    #[MapName('visit_model')]
    public ?object $visit_model = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;

    public static function before(array &$attributes){
        $attributes['flag'] ??= 'CLINICAL_VISIT';
    }

    public static function after(self $data): self{
        $new = static::new();
        $props = &$data->props;

        $data->reference = $new->requestDTO(($data->reference_type == 'ExternalReferral') ? ExternalReferralData::class : InternalReferralData::class,$data->reference);
        return $data;
    }
}