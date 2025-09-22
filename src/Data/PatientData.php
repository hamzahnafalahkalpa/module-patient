<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\PatientData as DataPatientData;
use Hanafalah\ModulePatient\Contracts\Data\ProfilePhotoData;
use Hanafalah\ModulePayer\Contracts\Data\PayerData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PatientData extends Data implements DataPatientData{
    use HasRequestData;

    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('name')]
    #[MapName('name')]
    public ?string $name = null;

    #[MapInputName('reference_type')]
    #[MapName('reference_type')]
    public ?string $reference_type = null;

    #[MapInputName('reference_id')]
    #[MapName('reference_id')]
    public mixed $reference_id = null;

    #[MapInputName('reference')]
    #[MapName('reference')]
    public array|object|null $reference = null;

    #[MapInputName('patient_type_id')]
    #[MapName('patient_type_id')]
    public mixed $patient_type_id = null;

    #[MapInputName('card_identity')]
    #[MapName('card_identity')]
    public ?CardIdentityData $card_identity = null;

    #[MapInputName('payer_id')]
    #[MapName('payer_id')]
    public mixed $payer_id = null;

    #[MapInputName('payer')]
    #[MapName('payer')]
    public ?PayerData $payer;

    #[MapInputName('profile')]
    #[MapName('profile')]
    public string|UploadedFile|null $profile = null;

    #[MapInputName('profile_dto')]
    #[MapName('profile_dto')]
    public ?ProfilePhotoData $profile_dto = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;

    public static function before(array &$attributes){
        $new = static::new();
        if (isset($attributes['id'])){
            $patient_model   = $new->PatientModel()->with('reference')->findOrFail($attributes['id']);
            $attributes['reference_id']   = $reference['id'] = $patient_model->reference_id;
            $attributes['reference_type'] = $patient_model->reference_type;
        }else{
            $config_keys = array_keys(config('module-patient.patient_types'));
            $keys        = array_intersect(array_keys($attributes),$config_keys);
            $key         = array_shift($keys);
            $attributes['reference_type'] ??= $key;
        }
        $attributes['reference_type'] = Str::studly($attributes['reference_type']);
    }

    public static function after(self $data): self{
        $new = static::new();
        if (isset($data->reference)){
            $reference = &$data->reference;
            $reference = self::transformToData($data->reference_type, $reference);
            $data->name = $reference->name;
        }

        $props = &$data->props;
        if (isset($data->payer_id) || isset($data->payer)){
            if (isset($data->payer_id)){
                $payer = $new->PayerModel()->withoutGlobalScopes()->findOrFail($data->payer_id);
                $payer_data = $payer->toArray();
                $data->payer = $new->requestDTO(PayerData::class,$payer_data);
                // $data->payer = $new->requestDTO(PayerData::class,[
                //     'id' => $data->payer_id,
                //     'name' => $payer->name,
                //     'flag' => $payer->flag,
                //     'l' => $payer->l,
                //     'is_payer_able' => true
                // ]);
            }
            $data->payer->props['is_payer_able'] = true;
        }
        $patient_type = $new->PatientTypeModel();
        $patient_type = (isset($data->patient_type_id)) ? $patient_type->findOrFail($data->patient_type_id) : $patient_type;
        $props['prop_patient_type'] = $patient_type->toViewApiOnlies('id','name','flag','label');
        return $data;
    }

    private static function transformToData(string $entity,array $attributes){
        $new = static::new();
        return $new->requestDTO(config('app.contracts.'.$entity.'Data'),$attributes);
    }
}