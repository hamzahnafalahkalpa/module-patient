<?php

namespace Hanafalah\ModulePatient\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;

interface PractitionerEvaluation extends DataManagement
{
    public function practitionerEvaluation(mixed $conditionals = null): Builder;
    public function getPractitionerEvaluation(): Model|LengthAwarePaginator|Collection;
    public function prepareStorePractitionerEvaluation(?array $attributes = null): Model;
    public function removePractitionerEvaluation(): bool;
    public function storePractitionerEvaluation(): array;
    public function showPractitionerEvaluation(?Model $model = null): array;
    public function viewPractitionerList(): array;
    public function addOrChange(?array $attributes = []): self;
}
