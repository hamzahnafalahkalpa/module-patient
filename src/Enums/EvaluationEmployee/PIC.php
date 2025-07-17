<?php

namespace Hanafalah\ModulePatient\Enums\EvaluationEmployee;

enum PIC: string
{
    case IS_PIC      = 'PIC';
    case IS_NURSE    = 'NURSE';
    case IS_DOCTOR   = 'DOCTOR';
    case IS_MIDWIFE  = 'MIDWIFE';
    case IS_PHARMACY = 'PHARMACY';
    case IS_OTHER    = 'OTHER';
}
