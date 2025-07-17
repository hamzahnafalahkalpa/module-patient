<?php

namespace Hanafalah\ModulePatient\Resources\PatientTypeHistory;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewPatientTypeHistory extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'    => $this->id,
            "name"  => $this->name,
        ];

        return $arr;
    }
}
