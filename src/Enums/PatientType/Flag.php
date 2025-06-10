<?php

namespace Hanafalah\ModulePatient\Enums\PatientType;

enum Flag: string
{
    case IDENTITY         = 'IDENTITY';
    case SERVICE          = 'SERVICE';
    case SERVICE_CLUSTER  = 'SERVICE_CLUSTER';
}
