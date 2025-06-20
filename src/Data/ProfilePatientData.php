<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\ModulePatient\Contracts\Data\ProfilePatientData as DataProfilePatientData;
use Hanafalah\ModulePeople\Contracts\Data\PeopleData;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class ProfilePatientData extends PatientData implements DataProfilePatientData{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('uuid')]
    #[MapName('uuid')]
    public ?string $uuid = null;
    
    #[MapInputName('name')]
    #[MapName('name')]
    public ?string $name = null;

    #[MapInputName('people')]
    #[MapName('people')]
    public ?PeopleData $people;

    #[MapInputName('profile')]
    #[MapName('profile')]
    public string|UploadedFile|null $profile = null;
        
    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;
}