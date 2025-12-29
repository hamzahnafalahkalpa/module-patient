<?php

namespace Hanafalah\ModulePatient\Resources\VisitRegistration;

class ShowVisitRegistration extends ViewVisitRegistration
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'warehouse' => $this->relationValidation('warehouse',function(){
                return $this->warehouse->toViewApi();
            },$this->prop_warehouse),
            'practitioner_evaluation' => $this->relationValidation('practitionerEvaluation', function () {
                return $this->practitionerEvaluation->toShowApi();
            },$this->prop_practitioner_evaluation),
            'practitioner_evaluations' => $this->relationValidation('practitionerEvaluations', function () {
                return $this->practitionerEvaluations->transform(function ($practitionerEvaluation) {
                    return $practitionerEvaluation->toShowApi();
                });
            }),
            'visit_patient'       => $this->relationValidation('visitPatient',function(){
                return $this->visitPatient->toShowApi()->resolve();
            },$this->prop_visit_patient),
            'services'            => $this->relationValidation('services', function () {
                return $this->services->transform(function ($service) {
                    return $service->toViewApi();
                });
            }),
            'examination_summary' => $this->relationValidation('examinationSummary', function () {
                return $this->examinationSummary->toShowApi()->resolve();
            }),
            'visit_registrations' => $this->relationValidation('visitRegistrations', function () {
                return $this->visitRegistrations->transform(function ($visitRegistration) {
                    return $visitRegistration->toShowApi();
                });
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);
        return $arr;
    }
}
