<?php

namespace Hanafalah\ModulePatient\Resources\VisitRegistration;

use Hanafalah\LaravelSupport\Resources\ApiResource;
use Hanafalah\ModulePatient\Resources\VisitPatient\ShowVisitPatient;

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
            "patient_type"            => $this->relationValidation('patientType', function () {
                $patientType = $this->patientType;
                return [
                    'id'   => $patientType->getKey(),
                    'name' => $patientType->name
                ];
            }),
            "status"          => $this->status,
            "status_spell"    => $this->getStatusSpell(),
            "medic_service"   => $this->relationValidation('medicService', function () {
                $medicService = $this->medicService;

                return [
                    'id'      => $medicService->getKey(),
                    'name'    => $medicService->name,
                    'flag'    => $medicService->flag,
                    'service' => $medicService->relationValidation('service', function () use ($medicService) {
                        return ['id' => $medicService->service->id];
                    })
                ];
            }),
            'visit_patient_type'     => $this->visit_patient_type,
            'visit_patient'          => $this->relationValidation('visitPatient', function () {
                return $this->visitPatient->toShowApi();
            }),
            'head_doctor'          => $this->relationValidation('headDoctor', function () {
                return $this->headDoctor->toShowApi();
            }),
            'visit_examination'  => $this->relationValidation('visitExamination', function () {
                return $this->visitExamination->toViewApi();
            }),
            'activity'           => $this->sortActivity(),
            'service_labels'     => $this->prop_service_labels ?? [],
            "created_at"         => $this->created_at,
            "updated_at"         => $this->updated_at
        ];

        return $arr;
    }
}
