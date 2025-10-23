<?php

namespace Hanafalah\ModulePatient\Resources\Patient;

use Hanafalah\LaravelSupport\Resources\ApiResource;
use Illuminate\Support\Str;

class ViewPatient extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $reference_type = Str::snake($this->reference_type);
        $profile = null;
        if (isset($this->profile)){
            $profile_path = profile_photo($this->profile);
            $disk = config('filesystems.default', 'public');
            if ($disk == 'public') $profile_path = $this->encryptName($profile_path);
            $profile = asset_url($profile_path);
        }
        $arr = [
            'id'               => $this->id,
            'uuid'             => $this->uuid,
            'name'             => $this->name,
            'medical_record'   => $this->medical_record,
            'profile'          => $profile,
            'patient_type_id'  => $this->patient_type_id,
            'patient_type'     => $this->prop_patient_type ?? null,
            $reference_type    => $this->relationValidation('reference',function(){
                return $this->reference->toViewApi()->resolve();
            },$this->{'prop_'.$reference_type} ?? null),
            'card_identity'    => $this->prop_card_identity,
            'payer_id'         => $this->payer_id,
            'payer'            => $this->relationValidation('payer',function(){
                return $this->payer->toViewApiOnlies('id','name','flag','label');
            },$this->prop_payer)
        ];
        return $arr;
    }
}
