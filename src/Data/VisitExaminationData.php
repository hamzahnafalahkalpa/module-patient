<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModuleMonitoring\Data\ModelHasMonitoringData;
use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationData as DataVisitExaminationData;
use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationPropsData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Hanafalah\ModulePatient\Contracts\Data\PatientData;

class VisitExaminationData extends Data implements DataVisitExaminationData{
    use HasRequestData;

    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('visit_examination_model')]
    #[MapName('visit_examination_model')]
    public ?object $visit_examination_model = null;

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

    #[MapInputName('patient_id')]
    #[MapName('patient_id')]
    public mixed $patient_id = null;

    #[MapInputName('patient')]
    #[MapName('patient')]
    public ?PatientData $patient = null;

    #[MapInputName('sign_off')]
    #[MapName('sign_off')]
    public ?bool $sign_off = false;

    #[MapInputName('sign_off_at')]
    #[MapName('sign_off_at')]
    public ?string $sign_off_at = null;

    #[MapInputName('is_addendum')]
    #[MapName('is_addendum')]
    public ?bool $is_addendum = false;

    #[MapInputName('model_has_monitorings')]
    #[MapName('model_has_monitorings')]
    #[DataCollectionOf(ModelHasMonitoringData::class)]
    public ?array $model_has_monitorings = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?VisitExaminationPropsData $props = null;

    public static function before(array &$attributes){
        $new = static::new();

        $attributes['sign_off'] ??= false;
        $attributes['is_addendum'] ??= false;
        if ($attributes['sign_off']) {
            $attributes['sign_off_at'] ??= now();
        }

        if (isset($attributes['id']) && !isset($attributes['visit_patient_id'])){
            $visit_examination_model = $new->VisitExaminationModel()->findOrFail($attributes['id']);
            $attributes['visit_patient_id'] = $visit_examination_model->visit_patient_id;
            $attributes['visit_registration_id'] = $visit_examination_model->visit_registration_id;
        }

        if (isset($attributes['examination']) && is_array($attributes['examination'])){
            $practitioner_evaluations = $attributes['practitioner_evaluations'] ?? [];
            $attributes['examination']['practitioner_evaluations'] = $practitioner_evaluations;
            if (isset($attributes['id'])) $attributes['examination']['visit_examination_id'] = $attributes['id'];
            $attributes['examination'] = $new->requestDTO(config('app.contracts.ExaminationData'),$attributes['examination']);
        }

        $patient_model = $new->PatientModel();
        if (isset($attributes['patient_id'])){
            $patient_model = $patient_model->find($attributes['patient_id']);
        }
        $attributes['prop_patient'] = $patient_model->toViewApi()->resolve();
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