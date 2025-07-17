<?php

namespace Hanafalah\ModulePatient\Resources\FamilyRelationship;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewFamilyRelationship extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'                => $this->id,
            "name"              => $this->name,
            "phone"             => $this->phone,
            "role"              => (int) $this->role,
            "reference_id"      => $this->reference_id,
            "reference_type"    => $this->reference_type,
            "sex"               => $this->sex
        ];
        if (class_exists(\Hanafalah\ModulePeople\Models\People\People::class)) {
            if ($this->reference_type == $this->PeopleModel()->getMorphClass()) {
                $arr['reference'] = $this->propResource($this->PeopleModel(), \Hanafalah\ModulePeople\Resources\People\ViewPeople::class);
            }
        }

        return $arr;
    }
}
