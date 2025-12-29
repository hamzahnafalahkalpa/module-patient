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
            'visit_patient'           => $this->relationValidation('visitPatient', function () {
                return $this->visitPatient->toViewApi();
            },$this->prop_visit_patient),
            'practitioner_evaluation' => $this->relationValidation('practitionerEvaluation', function () {
                return $this->practitionerEvaluation->toViewApi();
            },$this->prop_practitioner_evaluation),
            'visit_examination'       => $this->relationValidation('visitExamination', function () {
                return $this->visitExamination->toViewApi()->resolve();
            },$this->prop_visit_examination),
            'item_rents'              => $this->relationValidation('itemRents', function () {
                return $this->itemRents->transform(function ($itemRent) {
                    return $itemRent->toViewApi();
                });
            }),
            "status"                  => $this->status,
            'activity'                => $this->sortActivity(),
            'service_labels'          => $this->prop_service_labels ?? [],
            'warehouse_type'          => $this->warehouse_type,
            'warehouse_id'            => $this->warehouse_id,
            'assessments'             => $this->relationValidation('assessments', function () {
                return $this->assessments->transform(function ($assessment) {
                    return $assessment->toViewApi()->resolve();
                });
            }),
            'encounter_code'         => $this->encounter_code,
            'ihs_number'            => $this->ihs_number,
            "created_at"              => $this->created_at,
            "updated_at"              => $this->updated_at
        ];
        return $arr;
    }
}
