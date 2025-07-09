<?php

namespace Hanafalah\ModulePatient\Resources\ExternalReferral;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewExternalReferral extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'                => $this->id,
            'visit_patient'     => $this->prop_visit_patient,
            'doctor_name'       => $this->doctor_name,
            'note'              => $this->note
        ];
        return $arr;
    }
}
