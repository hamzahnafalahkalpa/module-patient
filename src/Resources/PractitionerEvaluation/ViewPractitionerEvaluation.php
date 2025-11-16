<?php

namespace Hanafalah\ModulePatient\Resources\PractitionerEvaluation;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewPractitionerEvaluation extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'                        => $this->id,
            'practitioner_reference_id' => $this->practitioner_id,
            'practitioner_reference'    => $this->relationValidation('practitioner', function () {
                return $this->practitioner->toViewApi()->resolve();
            },$this->prop_practitioner_reference),
            'profession_id'        => $this->profession_id,
            'profession'           => $this->prop_profession,
            'as_pic'               => $this->as_pic ?? false,
            'role_as'              => $this->role_as ?? null,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at
        ];

        return $arr;
    }
}
