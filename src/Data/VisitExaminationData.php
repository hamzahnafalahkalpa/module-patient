<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationData as DataVisitExaminationData;
use Hanafalah\ModulePatient\Contracts\Data\VisitRegistrationPropsData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class VisitExaminationData extends Data implements DataVisitExaminationData{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('visit_patient_id')]
    #[MapName('visit_patient_id')]
    public mixed $visit_patient_id = null;

    #[MapInputName('visit_registration_id')]
    #[MapName('visit_registration_id')]
    public mixed $visit_registration_id = null;

    #[MapInputName('practitioner_evaluations')]
    #[MapName('practitioner_evaluations')]
    #[DataCollectionOf(PractitionerEvaluationData::class)]
    public mixed $practitioner_evaluations = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;

    public static function after(self $data): self{
        $new = static::new();
        $props = &$data->props;

        return $data;
    }
}