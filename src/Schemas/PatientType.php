<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModuleService\Schemas\Service;
use Hanafalah\ModulePatient\Contracts\PatientType as ContractsPatientType;
use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Model
};
use Hanafalah\ModulePatient\Resources\PatientType\{
    ShowPatientType,
    ViewPatientType
};

class PatientType extends Service implements ContractsPatientType
{
    protected array $__guard   = ['id'];
    protected array $__add     = ['name'];
    protected string $__entity = 'PatientType';
    public static $patient_type_model;

    protected array $__resources = [
        'view' => ViewPatientType::class,
        'show' => ShowPatientType::class
    ];

    protected array $__cache = [
        'index' => [
            'name'     => 'patient-type',
            'tags'     => ['patient-type', 'patient-type-index'],
            'forever'  => true
        ]
    ];

    protected function showUsingRelation()
    {
        return [];
    }

    public function prepareShowPatientType(?Model $model = null, ?array $attributes = null): Model
    {
        $attributes ??= \request()->all();

        $model ??= $this->getPatientType();
        if (!isset($model)) {
            $id = $attributes['id'] ?? null;
            if (!isset($id)) throw new \Exception('No id provided', 422);

            $model = $this->patientType()->with($this->showUsingRelation())->findOrFail($id);
        } else {
            $model->load($this->showUsingRelation());
        }

        return static::$patient_type_model = $model;
    }

    public function showPatientType(?Model $model = null): array
    {
        return $this->transforming($this->__resources['show'], function () use ($model) {
            return $this->prepareShowPatientType($model);
        });
    }

    public function prepareStorePatientType(?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        if (!isset($attributes['id'])) {
            $patient_type = $this->PatientTypeModel()->updateOrCreate([
                'name' => $attributes['name']
            ]);
        } else {
            $patient_type = $this->PatientTypeModel()->findOrFail($attributes['id']);
            if (!$patient_type->is_permanent) {
                $patient_type->name = $attributes['name'];
                $patient_type->save();
            }
        }
        return static::$patient_type_model = $patient_type;
    }

    public function storePatientType(): array
    {
        return $this->transaction(function () {
            return $this->showPatientType($this->prepareStorePatientType());
        });
    }

    public function viewServicePatientTypeList(): array
    {
        return $this->transforming($this->__resources['view'], fn() => $this->prepareViewPatientTypeList());
    }

    public function prepareViewPatientTypeList(): Collection
    {
        return static::$patient_type_model = $this->cacheWhen(!$this->isSearch(), $this->__cache['index'], function () {
            return $this->patientType()->orderBy('name', 'asc')->get();
        });
    }

    public function viewPatientTypeList(): array
    {
        return $this->transforming($this->__resources['view'], fn() => $this->prepareViewPatientTypeList());
    }

    public function prepareDeletePatientType(?array $attributes = null): bool
    {
        $attributes ??= request()->all();
        $id = $attributes['id'] ?? null;
        if (!isset($id)) throw new \Exception('No id provided', 422);

        $patient_type = $this->patientType()->findOrFail($id);
        if ($patient_type->is_permanent) {
            return false;
        }
        return $patient_type->delete();
    }

    public function deletePatientType(): bool
    {
        return $this->transaction(function () {
            return $this->prepareDeletePatientType();
        });
    }

    public function patientType(mixed $conditionals = null): Builder
    {
        $this->booting();
        return $this->PatientTypeModel()->conditionals($conditionals);
    }

    public function getPatientType(): mixed
    {
        return static::$patient_type_model;
    }
}
