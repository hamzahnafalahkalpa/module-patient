<?php

namespace Hanafalah\ModulePatient\Resources\PractitionerEvaluation;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewPractitionerEvaluation extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'                        => $this->id,
            'name' => $this->name,
            'practitioner_id' => $this->practitioner_id,
            'practitioner'    => $this->relationValidation('practitioner', function () {
                return $this->practitioner->toViewApi();
            },$this->prop_practitioner),
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
