<?php

namespace Hanafalah\ModulePatient\Resources\VisitExamination;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewVisitExamination extends ApiResource
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
            'id'                     => $this->id,
            'visit_examination_code' => $this->visit_examination_code,
            'visit_patient_id'       => $this->visit_patient_id,
            'visit_registration_id'  => $this->visit_registration_id,
            'patient_id'             => $this->patient_id,
            'sign_off_at'            => $this->sign_off_at,
            'is_addendum'            => $this->is_addendum,
            'created_at'             => $this->created_at
        ];
        return $arr;
    }
}
