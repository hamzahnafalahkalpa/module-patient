<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\UpdateVisitRegistrationData as DataUpdateVisitRegistrationData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Hanafalah\ModulePatient\Enums\VisitRegistration\Status;

class UpdateVisitRegistrationData extends Data implements DataUpdateVisitRegistrationData{
    #[MapInputName('id')]
    #[MapName('id')]
    #[RequiredWithout('visit_registration_model')]
    public mixed $id = null;

    #[MapInputName('visit_registration_model')]
    #[MapName('visit_registration_model')]
    #[RequiredWithout('id')]
    public mixed $visit_registration_model = null;

    #[MapInputName('status')]
    #[MapName('status')]
    #[Enum(Status::class)]
    public mixed $status = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;
}