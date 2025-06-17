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
            'profile'          => $this->profile ?? null,
            'medical_record'   => $this->medical_record,
            'profile'          => $this->profile ?? null,
            'patient_type'     => $this->prop_patient_type ?? null,
            $reference_type    => $this->{'prop_'.$reference_type} ?? null,
            'card_identity'    => $this->prop_card_identity
        ];
        // if (class_exists(\Hanafalah\ModulePeople\Models\People\People::class)) {
        //     if ($this->reference_type == $this->PeopleModel()->getMorphClass()) {
        //         $arr['people']            = $this->propResource($this->reference, \Hanafalah\ModulePeople\Resources\People\ViewPeople::class);
        //         $arr['people']['phone_1'] = $this->phone_1;
        //         $arr['people']['phone_2'] = $this->phone_2;
        //     }
        // }


        return $arr;
    }
}
