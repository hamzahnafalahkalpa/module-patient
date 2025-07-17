<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\PractitionerEvaluationData as DataPractitionerEvaluationData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class PractitionerEvaluationData extends Data implements DataPractitionerEvaluationData{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('name')]
    #[MapName('name')]
    public ?string $name = null;

    #[MapInputName('reference_type')]
    #[MapName('reference_type')]
    public mixed $reference_type = null;

    #[MapInputName('reference_id')]
    #[MapName('reference_id')]
    public mixed $reference_id = null;

    #[MapInputName('practitioner_type')]
    #[MapName('practitioner_type')]
    public ?string $practitioner_type = null;

    #[MapInputName('practitioner_id')]
    #[MapName('practitioner_id')]
    public ?string $practitioner_id = null;

    #[MapInputName('profession_id')]
    #[MapName('profession_id')]
    public mixed $profession_id = null;

    #[MapInputName('as_pic')]
    #[MapName('as_pic')]
    public ?bool $as_pic = false;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;

    public static function after(self $data): self{
        $new = static::new();
        $props = &$data->props;
        
        $data->as_pic ??= false;
        return $data;
    }
}