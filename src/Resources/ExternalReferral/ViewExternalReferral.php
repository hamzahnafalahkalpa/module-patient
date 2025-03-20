<?php

namespace Hanafalah\ModulePatient\Resources\ExternalReferral;

use App\Http\Resources\ApiResource;

class ViewExternalReferral extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'                => $this->id,
            'visit_patient'     => $this->relationValidation('visitPatient', function () {
                return $this->visitPatient->toViewApi();
            }),
            'doctor_name'       => $this->doctor_name,
            'note'              => $this->note
        ];

        return $arr;
    }
}
