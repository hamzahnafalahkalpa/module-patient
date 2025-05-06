<?php

namespace Hanafalah\ModulePatient\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;

interface VisitRegistration extends DataManagement
{
    public function prepareStoreVisitRegistration(?array $attributes = null): Model;
    public function createVisitRegistration(array $attributes): Model;
    public function newVisitRegistration(?array $attributes = null): Model;
    public function storeServices($attributes);
    public function getVisitRegistration(): mixed;
    public function prepareShowVisitRegistration(?Model $model = null): ?Model;
    public function showVisitRegistration(?Model $model = null): array;
    public function storeVisitRegistration(): array;
    public function visitRegistration(mixed $conditionals = null): Builder;
    public function commonPaginate($paginate_options): LengthAwarePaginator;
    public function prepareVisitRegistrationPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): LengthAwarePaginator;
    public function viewVisitRegistrationPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null);
    public function getVisitRegistrations();
    public function addOrChange(?array $attributes = []): self;
}
