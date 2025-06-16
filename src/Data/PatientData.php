<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\PatientData as DataPatientData;
use Hanafalah\ModulePeople\Contracts\Data\PeopleData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Illuminate\Support\Str;

class PatientData extends Data implements DataPatientData{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('reference_type')]
    #[MapName('reference_type')]
    public ?string $reference_type = null;

    #[MapInputName('reference_id')]
    #[MapName('reference_id')]
    public mixed $reference_id = null;

    #[MapInputName('card_identity')]
    #[MapName('card_identity')]
    public ?CardIdentityData $card_identity = null;

    #[MapInputName('profile')]
    #[MapName('profile')]
    public ?string $profile = null;

    #[MapInputName('people')]
    #[MapName('people')]
    public ?PeopleData $people;

    public static function after(self $data): self{
        $new = static::new();
        if (isset($data->id)){
            $patient_model            = $new->PatientModel()->with('reference')->findOrFail($data->id);
            $patient_ref              = Str::snake($patient_model->reference_type);
            $data->{$patient_ref}->id = $patient_model->reference_id;
        }else{
            $data->fill(request()->only(array_keys(request()->all())));
            $data->reference_type ??= request()->reference_type;
        }
        return $data;
    }
}