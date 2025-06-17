<?php

namespace Hanafalah\ModulePatient\Enums\Patient;

enum CardIdentity: string
{
    case MEDICAL_RECORD     = 'medical_record';
    case OLD_MEDICAL_RECORD = 'old_medical_record';
    case IHS_NUMBER         = 'ihs_number';
    case BPJS               = 'bpjs';
}
