<?php

namespace Hanafalah\ModulePatient\Resources\Patient;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewPatient extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'               => $this->id,
            'profile'          => $this->profile ?? null,
            'medical_record'   => $this->medical_record,
            'user_reference'   => $this->when(isset($this->uuid), function () {
                return ['uuid' => $this->uuid];
            }),
            "card_identities"  => $this->cardIdentities->mapWithKeys(function ($cardIdentity) {
                return [$cardIdentity->flag => $cardIdentity->value];
            })
        ];
        if (class_exists(\Hanafalah\ModulePeople\Models\People\People::class)) {
            if ($this->reference_type == $this->PeopleModel()->getMorphClass()) {
                $arr['people']            = $this->propResource($this->reference, \Hanafalah\ModulePeople\Resources\People\ViewPeople::class);
                $arr['people']['phone_1'] = $this->phone_1;
                $arr['people']['phone_2'] = $this->phone_2;
            }
        }


        return $arr;
    }
}
