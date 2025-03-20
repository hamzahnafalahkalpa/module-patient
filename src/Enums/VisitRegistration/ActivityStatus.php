<?php

namespace Hanafalah\ModulePatient\Enums\VisitRegistration;

enum ActivityStatus: int
{
    case POLI_EXAM_QUEUE      = 1;
    case POLI_EXAM_START      = 2;
    case POLI_EXAM_END        = 3;
    case POLI_SESSION_START   = 11;
    case POLI_SESSION_END     = 12;

    case POLI_SESSION_CANCEL  = 22;

    case REFERRAL_CREATED     = 14;
    case REFERRAL_PROCESSED   = 17;
    case REFERRAL_DONE       = 20;
}
