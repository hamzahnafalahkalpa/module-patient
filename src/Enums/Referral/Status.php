<?php

namespace Zahzah\ModulePatient\Enums\Referral;

enum Status: string {
    case CREATED = 'CREATED';
    case PROCESS = 'PROCESS';
    CASE DONE    = 'DONE';
}
