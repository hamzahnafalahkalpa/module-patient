<?php

namespace Hanafalah\ModulePatient\Contracts;

use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Model
};
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;

interface PatientType extends DataManagement
{
    public function prepareShowPatientType(?Model $model = null, ?array $attributes = null): Model;
    public function showPatientType(?Model $model = null): array;
    public function prepareStorePatientType(?array $attributes = null): Model;
    public function storePatientType(): array;
    public function viewServicePatientTypeList(): array;
    public function prepareViewPatientTypeList(): Collection;
    public function viewPatientTypeList(): array;
    public function patientType(mixed $conditionals = null): Builder;
    public function getPatientType(): mixed;
    public function prepareDeletePatientType(?array $attributes = null): bool;
    public function deletePatientType(): bool;
}
