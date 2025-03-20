<?php

namespace Hanafalah\ModulePatient\Schemas;

use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\ModulePatient\{
    Contracts\VisitRegistration as ContractsVisitRegistration,
};

class ExaminationVisitRegistration extends VisitRegistration implements ContractsVisitRegistration
{
    public static $exam_visit_registration_model;

    public function commonPaginate($paginate_options): LengthAwarePaginator
    {
        return $this->visitRegistration()->orderBy('created_at', 'desc')->paginate(
            ...$this->arrayValues($paginate_options)
        )->appends(request()->all());
    }
}
