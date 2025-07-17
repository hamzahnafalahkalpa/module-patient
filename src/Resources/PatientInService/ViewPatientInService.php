<?php

namespace Hanafalah\ModulePatient\Resources\PatientInService;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewPatientInService extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'             => $this->id,
            'profile'        => $this->profile ?? null,
            'user_reference' => $this->when(isset($this->uuid), function () {
                return ['uuid' => $this->uuid];
            }),
            "card_identities"    => $this->cardIdentities->mapWithKeys(function ($cardIdentity) {
                return [$cardIdentity->flag => $cardIdentity->value];
            }),
        ];
        if (class_exists(\Hanafalah\ModulePeople\Models\People\People::class)) {
            if ($this->reference_type == $this->PeopleModel()->getMorphClass()) {
                $arr['people'] = $this->propResource($this->reference, \Hanafalah\ModulePeople\Resources\People\ViewPeople::class);
            }
        }


        return $arr;
    }
}
