<?php

namespace Hanafalah\ModulePatient\Enums\VisitPatient;

enum ActivityStatus: int
{
    case ADM_START      = 1;
    case ADM_PROCESSED  = 2;
    case ADM_FINISHED   = 3;
    case ADM_CANCELLED  = 4;
}
