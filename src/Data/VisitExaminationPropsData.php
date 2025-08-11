<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationPropsData as DataVisitExaminationPropsData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class VisitExaminationPropsData extends Data implements DataVisitExaminationPropsData{
    #[MapInputName('assessment')]
    #[MapName('assessment')]
    public null|array|object $assessment = null;

    #[MapInputName('treatments')]
    #[MapName('treatments')]
    public ?array $treatments = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;

    public static function before(array &$attributes){
        $new = static::new();
        if (isset($attributes['treatments']) && is_array($attributes['examination'])){
            foreach ($attributes['treatments'] as &$treatment) {
                if (is_array($treatment)) $treatment = $new->requestDTO(config('app.contracts.AssessmentData'),$treatment);
            }
        }
    }
}