<?php

namespace Hanafalah\ModulePatient\Enums\VisitRegistration;

enum RegistrationStatus: string
{
    case DRAFT      = 'DRAFT';
    case PROCESSING = 'PROCESSING';
    case COMPLETED  = 'COMPLETED';
    case CANCELLED  = 'CANCELLED';
}
