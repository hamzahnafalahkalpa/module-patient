<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\VisitPatientData as DataVisitPatientData;
use Hanafalah\ModulePatient\Contracts\Data\ProfilePhotoData;
use Hanafalah\ModulePayer\Contracts\Data\PayerData;
use Hanafalah\ModulePeople\Contracts\Data\PeopleData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class VisitPatientData extends Data implements DataVisitPatientData{
    use HasRequestData;

    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('reference_type')]
    #[MapName('reference_type')]
    public ?string $reference_type = null;

    #[MapInputName('reference_id')]
    #[MapName('reference_id')]
    public mixed $reference_id = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;

    public static function after(self $data): self{
    }
}