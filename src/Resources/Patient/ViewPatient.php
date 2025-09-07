<?php

namespace Hanafalah\ModulePatient\Resources\Patient;

use Hanafalah\LaravelSupport\Resources\ApiResource;
use Illuminate\Support\Str;

class ViewPatient extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $reference_type = Str::snake($this->reference_type);
        $arr = [
            'id'               => $this->id,
            'uuid'             => $this->uuid,
            'name'             => $this->name,
            'profile'          => $this->profile ?? null,
            'medical_record'   => $this->medical_record,
            'profile'          => $this->profile ?? null,
            'patient_type_id'  => $this->patient_type_id,
            'patient_type'     => $this->prop_patient_type ?? null,
            'patient_occupation_id' => $this->patient_occupation_id,
            'patient_occupation' => $this->relationValidation('patientOccupation',function(){
                return $this->patientOccupation->toViewApi()->resolve();
            },$this->prop_patient_occupation),
            $reference_type    => $this->{'prop_'.$reference_type} ?? null,
            'card_identity'    => $this->prop_card_identity,
            'payer_id'         => $this->payer_id,
            'payer'            => $this->relationValidation('payer',function(){
                return $this->payer->toViewApiOnlies('id','name','flag','label');
            },$this->prop_payer)
        ];
        return $arr;
    }
}
