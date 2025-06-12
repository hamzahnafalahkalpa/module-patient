<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\ModuleMedicService\Data\MedicServiceData;
use Hanafalah\ModulePatient\Contracts\Data\PatientTypeData as DataPatientTypeData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class PatientTypeData extends MedicServiceData implements DataPatientTypeData{
    #[MapInputName('label')]
    #[MapName('label')]
    public ?string $label = 'UMUM';
    
    #[MapInputName('childs')]
    #[MapName('childs')]
    #[DataCollectionOf(PatientTypeData::class)]
    public array $childs = [];

    public static function after(mixed $data): PatientTypeData{
        $data->flag = 'IDENTITY';
        return $data;
    }
}