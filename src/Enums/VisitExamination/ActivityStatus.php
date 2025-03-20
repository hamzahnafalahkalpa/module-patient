<?php

namespace Zahzah\ModulePatient\Enums\VisitExamination;

enum ActivityStatus: int{
    case VISIT_CREATED = 1;
    case VISITING      = 2;
    case VISITED       = 3;
    case CANCELLED     = 4;
}