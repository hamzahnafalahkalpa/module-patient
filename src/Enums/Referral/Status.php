<?php

namespace Hanafalah\ModulePatient\Enums\Referral;

enum Status: string
{
    case CREATED = 'CREATED';
    case PROCESS = 'PROCESS';
    case DONE    = 'DONE';
}
