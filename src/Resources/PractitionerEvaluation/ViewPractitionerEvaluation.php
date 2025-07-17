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
            }),
            'is_commit'            => $this->is_commit,
            'role_as'              => $this->role_as,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at
        ];

        return $arr;
    }
}
