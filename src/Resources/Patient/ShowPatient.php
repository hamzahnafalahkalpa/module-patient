<?php

namespace Hanafalah\ModulePatient\Resources\Patient;

use Hanafalah\ModulePeople\Resources\People\ShowPeople;

class ShowPatient extends ViewPatient
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            "occupation" => $this->props_occupation,
        ];

        if (class_exists(\Hanafalah\ModulePeople\Models\People\People::class)) {
            if ($this->reference_type == $this->PeopleModel()->getMorphClass()) {
                $arr['people'] = $this->relationValidation('reference', function () {
                    $this->reference->phone_1 =  $this->phone_1;
                    $this->reference->phone_2 =  $this->phone_2;
                    return $this->reference->toShowApi();
                });
            }
        }

        $arr = array_merge(parent::toArray($request), $arr);

        return $arr;
    }
}
