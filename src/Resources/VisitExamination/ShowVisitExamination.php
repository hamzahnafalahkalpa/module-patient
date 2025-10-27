<?php

namespace Hanafalah\ModulePatient\Resources\VisitExamination;

class ShowVisitExamination extends ViewVisitExamination
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'visit_registration' => $this->relationValidation('visitRegistration', function () {
                return $this->visitRegistration->toShowApi()->resolve();
            }),
            'practitioner_evaluations' => $this->relationValidation('practitionerEvaluations', function () {
                return $this->practitionerEvaluations->transform(function ($practitionerEvaluation) {
                    return $practitionerEvaluation->toViewApi();
                });
            }),
            'screening_summaries' => $this->screening_summaries,
            'form_summaries'      => $this->form_summaries,
            "addendum"            =>  $this->examinationSummary->addendum ?? null,
            'examination'         => $this->examination ?? null,
            'examination_summary' => $this->relationValidation('examinationSummary', function () {
                return $this->examinationSummary->toShowApi()->resolve();
            })
        ];
        if (isset($this->screenings)) {
            $arr['screenings'] = $this->screenings;
        }
        if (isset($this->forms)) {
            $arr['forms']      = $this->forms;
        }
        $arr = array_merge(parent::toArray($request), $arr);

        return $arr;
    }
}
