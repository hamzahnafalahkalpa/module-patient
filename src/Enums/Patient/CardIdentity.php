<?php

namespace Hanafalah\ModulePatient\Enums\Patient;

enum CardIdentity: string
{
    case OLD_MEDICAL_RECORD = 'old_mr';
    case IHS_NUMBER         = 'ihs_number';
    case BPJS               = 'bpjs';
}
