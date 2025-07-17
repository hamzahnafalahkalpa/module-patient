<?php

namespace Hanafalah\ModulePatient\Enums\VisitPatient;

enum VisitStatus: string
{
    case ACTIVE     = 'ACTIVE';
    case CANCELLED  = 'CANCELLED';
    case COMPLETED  = 'COMPLETED';
}
