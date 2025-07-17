<?php

namespace Hanafalah\ModulePatient\Resources\PatientType;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewPatientType extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'         => $this->id,
            'parent_id'  => $this->parent_id,
            'name'       => $this->name,
            'flag'       => $this->flag,
            'label'      => $this->label,
            'service'    => $this->relationValidation('service',function(){
                return $this->service->toViewApi()->resolve();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
        return $arr;
    }
}
