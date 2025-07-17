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
            'patient_type'     => $this->prop_patient_type ?? null,
            $reference_type    => $this->{'prop_'.$reference_type} ?? null,
            'card_identity'    => $this->prop_card_identity
        ];
        return $arr;
    }
}
