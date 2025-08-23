<?php

namespace Hanafalah\ModulePatient\Enums\VisitRegistration;

enum Activity: string
{
    case POLI_EXAM      = 'poli_exam';
    case POLI_SESSION   = 'poli_session';
    case REFERRAL_POLI  = 'referral_poli';
}
