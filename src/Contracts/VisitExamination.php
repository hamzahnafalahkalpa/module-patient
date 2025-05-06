<?php

namespace Hanafalah\ModulePatient\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;

interface VisitExamination extends DataManagement
{
    public function prepareCommitVisitExamination(?array $attributes = null): Model;
    public function commitVisitExamination(): array;
    public function prepareStoreVisitExamination(?array $attributes = null): Model;
    public function prepareViewVisitExaminationList(?array $attributes = null): Collection;
    public function viewVisitExaminationList(): array;
    public function getVisitExamination(): mixed;
    public function prepareShowVisitExamination(?Model $model = null): Model;
    public function showVisitExamination(): array;
    public function visitExamination(mixed $conditionals = null): Builder;
    public function addOrChange(?array $attributes = []): self;
}
