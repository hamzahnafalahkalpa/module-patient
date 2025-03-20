<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModulePatient\Contracts\ExaminationSummary as ContractsExaminationSummary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Supports\PackageManagement;

class ExaminationSummary extends PackageManagement implements ContractsExaminationSummary
{

    protected string $__entity = 'ExaminationSummary';
    public static $examination_summary;

    public function examinationSummary(mixed $conditionals = null): builder
    {
        $this->booting();
        return $this->ExaminationSummaryModel()->conditionals($conditionals)->withParameters();
    }

    public function prepareStoreExaminationSummary(?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        if (isset($attributes['id'])) {
            $guard = ['id' => $attributes['id']];
        } else {
            if (!isset($attributes['reference_type']) || !isset($attributes['reference_id'])) throw new \Exception('reference_type and reference_id is required', 422);
            $guard = ['reference_type' => $attributes['reference_type'], 'reference_id' => $attributes['reference_id']];
        }

        $model = $this->ExaminationSummaryModel()->firstOrCreate($guard);
        $fillable = $this->diff($model->getFillable(), ['id', 'created_at', 'updated_at']);
        foreach ($attributes as $key => $value) {
            if (!in_array($key, $fillable)) $model->$key = $value;
        }
        $model->save();
        return static::$examination_summary = $model;
    }
}
