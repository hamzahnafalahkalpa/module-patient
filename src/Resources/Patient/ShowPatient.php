<?php

namespace Hanafalah\ModulePatient\Resources\Patient;

use Illuminate\Support\Str;

class ShowPatient extends ViewPatient
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $reference_type = Str::snake($this->reference_type);
        $arr = [
            'patient_occupation' => $this->props_patient_occupation,
            $reference_type => $this->relationValidation('reference', function () {
                return $this->reference->toShowApi()->resolve();
            })
        ];

        // if (class_exists(\Hanafalah\ModulePeople\Models\People\People::class)) {
        //     if ($this->reference_type == $this->PeopleModel()->getMorphClass()) {
        //         $arr['people'] = $this->relationValidation('reference', function () {
        //             $this->reference->phone_1 =  $this->phone_1;
        //             $this->reference->phone_2 =  $this->phone_2;
        //             return $this->reference->toShowApi()->resolve();
        //         });
        //     }
        // }

        $arr = $this->mergeArray(parent::toArray($request), $arr);
        return $arr;
    }
}
