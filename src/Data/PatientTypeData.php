<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\PatientTypeData as DataPatientTypeData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class PatientTypeData extends Data implements DataPatientTypeData{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;
    
    #[MapInputName('parent_id')]
    #[MapName('parent_id')]
    public mixed $parent_id = null;

    #[MapInputName('name')]
    #[MapName('name')]
    public string $name;
    
    #[MapInputName('flag')]
    #[MapName('flag')]
    public string $flag;

    #[MapInputName('label')]
    #[MapName('label')]
    public string $label = 'UMUM';
    
    #[MapInputName('childs')]
    #[MapName('childs')]
    #[DataCollectionOf(PatientTypeData::class)]
    public array $childs = [];

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = [];
}