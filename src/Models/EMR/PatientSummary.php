<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Hanafalah\ModuleSummary\Models\Summary\Summary;

class PatientSummary extends Summary
{
    protected $table = 'summaries';

    protected $list      = ['id', 'parent_id', 'patient_id', 'reference_type', 'reference_id', 'props'];
}
