<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationData as DataVisitExaminationData;
use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationPropsData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class VisitExaminationData extends Data implements DataVisitExaminationData{
    use HasRequestData;

    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('visit_patient_id')]
    #[MapName('visit_patient_id')]
    public mixed $visit_patient_id = null;

    #[MapInputName('visit_registration_id')]
    #[MapName('visit_registration_id')]
    public mixed $visit_registration_id = null;

    #[MapInputName('visit_registration_model')]
    #[MapName('visit_registration_model')]
    public ?object $visit_registration_model = null;

    #[MapInputName('visit_patient_model')]
    #[MapName('visit_patient_model')]
    public ?object $visit_patient_model = null;

    #[MapInputName('examination')]
    #[MapName('examination')]
    public array|object|null $examination = null;

    #[MapInputName('practitioner_evaluations')]
    #[MapName('practitioner_evaluations')]
    #[DataCollectionOf(PractitionerEvaluationData::class)]
    public ?array $practitioner_evaluations = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?VisitExaminationPropsData $props = null;

    public static function before(array &$attributes){
        $new = static::new();
        if (isset($attributes['examination']) && is_array($attributes['examination'])){
            if (isset($attributes['id'])) $attributes['examination']['visit_examination_id'] = $attributes['id'];
            $attributes['examination'] = $new->requestDTO(config('app.contracts.ExaminationData'),$attributes['examination']);
        }
    }

    public static function after(self $data): self{
        $new = static::new();
        $props = &$data->props;
        if (isset($data->examination)){
            $examination = $data->examination;
            $data->visit_patient_model = $examination->visit_patient_model;
            $data->visit_registration_model = $examination->visit_registration_model;
        }
        return $data;
    }
}