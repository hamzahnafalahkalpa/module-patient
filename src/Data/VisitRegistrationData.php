<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\VisitRegistrationData as DataVisitRegistrationData;
use Hanafalah\ModulePatient\Contracts\Data\VisitRegistrationPropsData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class VisitRegistrationData extends Data implements DataVisitRegistrationData{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('visit_patient_id')]
    #[MapName('visit_patient_id')]
    public mixed $visit_patient_id = null;

    #[MapInputName('medic_service_id')]
    #[MapName('medic_service_id')]
    public mixed $medic_service_id = null;

    #[MapInputName('patient_type_service_id')]
    #[MapName('patient_type_service_id')]
    public mixed $patient_type_service_id = null;

    #[MapInputName('referral_id')]
    #[MapName('referral_id')]
    public mixed $referral_id = null;

    #[MapInputName('status')]
    #[MapName('status')]
    public mixed $status = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?VisitRegistrationPropsData $props = null;

    public static function after(self $data): self{
        $new = static::new();
        $props = &$data->props->props;
        $patient_type_service = $new->PatientTypeServiceModel();
        $patient_type_service = (isset($data->patient_type_service_id)) ? $patient_type_service->findOrFail($data->patient_type_service_id) : $patient_type_service;
        $props['prop_patient_type_service'] = $patient_type_service->toViewApi()->resolve();
        return $data;
    }
}