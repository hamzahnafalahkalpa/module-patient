<?php

namespace Hanafalah\ModulePatient\Resources\ExaminationSummary;

use Hanafalah\ModuleExamination\Resources\PatientSummary\ViewPatientSummary;

class ViewExaminationSummary extends ViewPatientSummary
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
            'id'                => $this->id, 
            'parent_id'          => $this->parent_id, 
            'patient_id'         => $this->patient_id, 
            'reference_type'     => $this->reference_type, 
            'reference_id'       => $this->reference_id, 
        ];
        return $arr;
    }
}
