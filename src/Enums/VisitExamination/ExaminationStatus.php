<?php

namespace Hanafalah\ModulePatient\Enums\VisitExamination;

enum ExaminationStatus: string
{
    case DRAFT     = 'DRAFT';
    case VISITING  = 'VISITING';
    case EXAMING   = 'EXAMING';
    case VISITED   = 'VISITED';
    case CANCELLED = 'CANCELLED';
}
