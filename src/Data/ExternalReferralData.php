<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\ExternalReferralData as DataExternalReferralData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class ExternalReferralData extends Data implements DataExternalReferralData {
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id;
    
    #[MapInputName('date')]
    #[MapName('date')]
    public ?string $date = null;

    #[MapInputName('doctor_name')]
    #[MapName('doctor_name')]
    public ?string $doctor_name = null;

    #[MapInputName('phone')]
    #[MapName('phone')]
    public ?string $phone = null;

    #[MapInputName('facility_name')]
    #[MapName('facility_name')]
    public ?string $facility_name = null;

    #[MapInputName('unit_name')]
    #[MapName('unit_name')]
    public ?string $unit_name = null;

    #[MapInputName('initial_diagnose')]
    #[MapName('initial_diagnose')]
    public ?string $initial_diagnose = null;

    #[MapInputName('primary_diagnose')]
    #[MapName('primary_diagnose')]
    public ?string $primary_diagnose = null;

    #[MapInputName('note')]
    #[MapName('note')]
    public ?string $note = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;
}