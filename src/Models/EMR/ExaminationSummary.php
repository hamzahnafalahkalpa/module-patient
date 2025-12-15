<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Hanafalah\ModulePatient\Resources\ExaminationSummary\{
    ShowExaminationSummary,
    ViewExaminationSummary
};

class ExaminationSummary extends PatientSummary
{
    protected $table = 'examination_summaries';

    public function getViewResource()
    {
        return ViewExaminationSummary::class;
    }

    public function getShowResource()
    {
        return ShowExaminationSummary::class;
    }
}
