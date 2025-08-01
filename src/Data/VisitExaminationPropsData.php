<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModuleExamination\Contracts\Data\AssessmentData;
use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationPropsData as DataVisitExaminationPropsData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class VisitExaminationPropsData extends Data implements DataVisitExaminationPropsData{
    #[MapInputName('assessment')]
    #[MapName('assessment')]
    public null|array|object $assessment = null;

    #[MapInputName('treatments')]
    #[MapName('treatments')]
    #[DataCollectionOf(AssessmentData::class)]
    public ?array $treatments = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;
}