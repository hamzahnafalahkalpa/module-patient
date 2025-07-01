<?php

namespace Hanafalah\ModulePatient\Resources\PractitionerEvaluation;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ShowPractitionerEvaluation extends ViewPractitionerEvaluation
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'practitioner_reference' => $this->relationValidation('practitioner', function () {
                return $this->practitioner->toShowApi()->resolve();
            })
        ];
        $arr = array_merge(parent::toArray($request), $arr);

        return $arr;
    }
}
