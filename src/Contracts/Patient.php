<?php

namespace Hanafalah\ModulePatient\Contracts;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface Patient extends DataManagement
{
    public function getPatientByUUID(?array $attributes = null): Model;
    public function prepareShowPatient(?Model $model = null): Model;
    public function showPatient(?Model $model = null): array;
    public function prepareStorePatient(?array $attributes = null): Model;
    public function storePatient(mixed $attributes = null): array;
    public function prepareViewPatientList(): LengthAwarePaginator;
    public function prepareViewFullPatientList(): LengthAwarePaginator;
    public function viewFullPatientList(): array;
    public function viewPatientList(): array;
    public function patient(mixed $conditionals = null): Builder;
    public function getPatients(mixed $conditionals = null): LengthAwarePaginator;
    public function addOrChange(?array $attributes = []): self;
}
