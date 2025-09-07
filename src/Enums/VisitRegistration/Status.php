<?php

namespace Hanafalah\ModulePatient\Enums\VisitRegistration;

enum Status: string
{
    case DRAFT      = 'DRAFT';
    case PROCESSING = 'PROCESSING';
    case COMPLETED  = 'COMPLETED';
    case CANCELLED  = 'CANCELLED';
}
