<?php

namespace Hanafalah\ModulePatient\Resources\VisitRegistration;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewVisitRegistration extends ApiResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            "id"                      => $this->id,
            "visit_registration_code" => $this->visit_registration_code,
            "medic_service"           => $this->prop_medic_service,
            'visit_patient_type'      => $this->visit_patient_type,
            'visit_patient'           => $this->prop_visit_patient,
            'practitioner'            => $this->prop_pracitioner,
            'visit_examination'       => $this->prop_visit_examination,
            'item_rents'              => $this->relationValidation('itemRents', function () {
                return $this->itemRents->transform(function ($itemRent) {
                    return $itemRent->toViewApi();
                });
            }),
            "status"                  => $this->status,
            'activity'                => $this->sortActivity(),
            'service_labels'          => $this->prop_service_labels ?? [],
            "created_at"              => $this->created_at,
            "updated_at"              => $this->updated_at
        ];
        return $arr;
    }
}
