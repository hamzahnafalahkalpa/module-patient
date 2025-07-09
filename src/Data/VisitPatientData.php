<?php

namespace Hanafalah\ModulePatient\Data;

use Carbon\Carbon;
use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\ReferralData;
use Hanafalah\ModulePatient\Contracts\Data\VisitPatientData as DataVisitPatientData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\DateFormat;

class VisitPatientData extends Data implements DataVisitPatientData{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('parent_id')]
    #[MapName('parent_id')]
    public mixed $parent_id = null;

    #[MapInputName('patient_id')]
    #[MapName('patient_id')]
    public mixed $patient_id;

    #[MapInputName('reference_type')]
    #[MapName('reference_type')]
    public ?string $reference_type = null;

    #[MapInputName('reference_id')]
    #[MapName('reference_id')]
    public mixed $reference_id = null;

    #[MapInputName('reservation_id')]
    #[MapName('reservation_id')]
    public mixed $reservation_id = null;

    #[MapInputName('patient_type_service_id')]
    #[MapName('patient_type_service_id')]
    public mixed $patient_type_service_id;

    #[MapInputName('queue_number')]
    #[MapName('queue_number')]
    public mixed $queue_number = null;

    #[MapInputName('visited_at')]
    #[MapName('visited_at')]
    #[DateFormat('Y-m-d')]
    public ?string $visited_at = null;

    #[MapInputName('reported_at')]
    #[MapName('reported_at')]
    public ?Carbon $reported_at = null;

    #[MapInputName('flag')]
    #[MapName('flag')]
    public string $flag;

    #[MapInputName('referral')]
    #[MapName('referral')]
    public ?ReferralData $referral = null;

    #[MapInputName('visit_registrations')]
    #[MapName('visit_registrations')]
    #[DataCollectionOf(VisitRegistrationData::class)]
    public ?array $visit_registrations;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?VisitPatientPropsData $props = null;

    public static function before(array &$attributes){
        $attributes['flag'] ??= 'CLINICAL_VISIT';
    }

    public static function after(self $data): self{
        $new = static::new();
        $props = &$data->props->props;

        $props['prop_patient'] = $new->PatientModel()->findOrFail($data->patient_id)->toViewApi()->resolve();
        $patient_type_service = $new->PatientTypeServiceModel();
        $patient_type_service = (isset($data->patient_type_service_id)) ? $patient_type_service->findOrFail($data->patient_type_service_id) : $patient_type_service;
        $props['prop_patient_type_service'] = $patient_type_service->toViewApi()->resolve();
        return $data;
    }
}