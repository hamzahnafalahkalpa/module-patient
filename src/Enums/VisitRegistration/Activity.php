<?php

namespace Hanafalah\ModulePatient\Enums\VisitRegistration;

enum Activity: string
{
    case POLI_EXAM      = 'POLI_EXAM';
    case POLI_SESSION   = 'POLI_SESSION';
    case REFERRAL_POLI  = 'REFERRAL_POLI';
}
