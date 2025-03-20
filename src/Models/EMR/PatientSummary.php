<?php

namespace Zahzah\ModulePatient\Models\EMR;

use Zahzah\ModuleSummary\Models\Summary\Summary;

class PatientSummary extends Summary{
    protected $table = 'summaries';

    protected $list      = ['id','parent_id','patient_id','reference_type','reference_id','props'];
}