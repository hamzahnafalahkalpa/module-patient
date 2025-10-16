<?php

namespace Hanafalah\ModulePatient\Resources\Patient;

use Illuminate\Support\Str;

class ShowPatient extends ViewPatient
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $reference_type = Str::snake($this->reference_type);
        $arr = [
            $reference_type    => $this->relationValidation('reference',function(){
                return $this->reference->toShowApi()->resolve();
            },$this->{'prop_'.$reference_type} ?? null),
            'patient_occupation_id' => $this->patient_occupation_id,
            'patient_occupation' => $this->relationValidation('patientOccupation',function(){
                return $this->patientOccupation->toShowApi()->resolve();
            },$this->prop_patient_occupation),
            'patient_summary' => $this->relationValidation('patientSummary',function(){
                return $this->patientSummary->toShowApi()->resolve();
            }),
            'visit_examination' => $this->relationValidation('visitExamination',function(){
                return $this->visitExamination->toShowApi()->resolve();
            })
        ];

        $arr = $this->mergeArray(parent::toArray($request), $arr);
        return $arr;
    }
}
