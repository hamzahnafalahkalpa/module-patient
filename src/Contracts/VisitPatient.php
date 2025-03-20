<?php

namespace Hanafalah\ModulePatient\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\LaravelSupport\Contracts\DataManagement;

interface VisitPatient extends DataManagement
{
    public function preparePushLifeCycleActivity(Model $visit_patient, Model $visit_patient_model, mixed $activity_status, int|array $statuses): self;
    public function prepareStoreVisitPatient(?array $attributes = null): Model;
    public function storeVisitPatient(): array;
    public function showUsingRelation(): array;
    public function prepareShowVisitPatient(?Model $model = null, ?array $attributes = null): Model;
    public function showVisitPatient(?Model $model = null);
    public function prepareViewPatientPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): LengthAwarePaginator;
    public function viewVisitPatientPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): array;
    public function commonPaginate($paginate_options): LengthAwarePaginator;
    public function prepareDeleteVisitPatient(?array $attributes = null): mixed;
    public function deleteVisitPatient(): bool;
    public function visitPatient(mixed $conditionals = null): Builder;
    public function getVisitPatient(): mixed;
    public function addOrChange(?array $attributes = []): self;
}
