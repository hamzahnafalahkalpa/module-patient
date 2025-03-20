<?php

namespace Zahzah\ModulePatient\Enums\Patient;

enum CardIdentity: string{
    case MEDICAL_RECORD     = 'MR'; 
    case OLD_MEDICAL_RECORD = 'OLD_MR'; 
    case BPJS_CODE          = 'BPJS_CODE'; 
    case NIK                = 'NIK';
    case PASSPORT           = 'PASSPORT';
    case UHID               = 'UHID'; 
}