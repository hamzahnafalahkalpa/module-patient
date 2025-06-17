<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\PatientData as DataPatientData;
use Hanafalah\ModulePatient\Contracts\Data\ProfilePhotoData;
use Hanafalah\ModulePayer\Contracts\Data\PayerData;
use Hanafalah\ModulePeople\Contracts\Data\PeopleData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PatientData extends Data implements DataPatientData{
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

    #[MapInputName('patient_type_id')]
    #[MapName('patient_type_id')]
    public mixed $patient_type_id = null;

    #[MapInputName('card_identity')]
    #[MapName('card_identity')]
    public ?CardIdentityData $card_identity = null;

    #[MapInputName('people')]
    #[MapName('people')]
    public ?PeopleData $people;

    #[MapInputName('payer_id')]
    #[MapName('payer_id')]
    public mixed $payer_id = null;

    #[MapInputName('profile')]
    #[MapName('profile')]
    public string|UploadedFile|null $profile = null;

    #[MapInputName('profile_dto')]
    #[MapName('profile_dto')]
    public ?ProfilePhotoData $profile_dto = null;

    #[MapInputName('payer')]
    #[MapName('payer')]
    public ?PayerData $payer;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;

    public static function after(self $data): self{
        $new = static::new();
        if (isset($data->id)){
            $patient_model = $new->PatientModel()->with('reference')->findOrFail($data->id);
            $patient_ref   = Str::snake($patient_model->reference_type);
            $data->{$patient_ref}->id = $patient_model->reference_id;
        }else{
            $config_keys = array_keys(config('module-patient.patient_types'));
            $keys        = array_intersect(array_keys(request()->all()),$config_keys);
            $key         = array_shift($keys);
            $data->reference_type ??= request()->reference_type ?? $key;
            $data->reference_type = Str::studly($data->reference_type);
        }
        $data->props['prop_payer'] = [
            'id'   => $data->payer_id ?? null,
            'name' => null,
            'flag' => null
        ];
        if (isset($data->payer_id) || isset($data->payer)){
            if (isset($data->payer_id)){
                $data->payer = $new->requestDTO(PayerData::class,[
                    'id' => $data->payer_id,
                    'is_payer_able' => true
                ]);
            }
            $data->payer->props['is_payer_able'] = true;
        }
        return $data;
    }
}