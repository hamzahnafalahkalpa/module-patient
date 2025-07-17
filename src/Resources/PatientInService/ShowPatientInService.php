<?php

namespace Hanafalah\ModulePatient\Resources\PatientInService;

class ShowPatientInService extends ViewPatientInService
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [];
        if (class_exists(\Hanafalah\ModulePeople\Models\People\People::class)) {
            if ($this->reference_type == $this->PeopleModel()->getMorphClass()) {
                $arr['people'] = $this->relationValidation('reference', function () {
                    return $this->reference->toShowApi()->resolve();
                });
            }
        }

        $arr = array_merge(parent::toArray($request), $arr);

        return $arr;
    }
}
