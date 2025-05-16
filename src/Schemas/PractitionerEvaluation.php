<?php

namespace Hanafalah\ModulePatient\Schemas;

use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Model
};
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\ModulePatient\Contracts\Schemas\{
    PractitionerEvaluation as ContractsPractitionerEvaluation
};
use Hanafalah\ModulePatient\Enums\{
    EvaluationEmployee\Commit,
    EvaluationEmployee\PIC
};
use Hanafalah\ModulePatient\ModulePatient;
use Hanafalah\ModulePatient\Resources\PractitionerEvaluation\{
    ShowPractitionerEvaluation,
    ViewPractitionerEvaluation
};

class PractitionerEvaluation extends ModulePatient implements ContractsPractitionerEvaluation
{
    protected string $__entity = 'PractitionerEvaluation';
    public static $practitioner_evaluation;

    public function practitionerEvaluation(mixed $conditionals = null): Builder
    {
        return $this->PractitionerEvaluationModel()
            ->with('practitioner.profession')
            ->conditionals($conditionals)->withParameters()
            ->orderBy('created_at', 'desc');
    }

    public function getPractitionerEvaluation(): Model|LengthAwarePaginator|Collection
    {
        return static::$practitioner_evaluation;
    }

    public function prepareStorePractitionerEvaluation(?array $attributes = null): Model
    {
        $attributes ??= request()->all();

        if (!isset($attributes['visit_examination_id'])) throw new \Exception('visit_examination_id is required', 422);
        if (!isset($attributes['practitioner_id'])) throw new \Exception('practitioner_id is required', 422);

        $practitioner_model = app(config('module-patient.practitioner'))->find($attributes['practitioner_id'] ?? null);

        $attributes['practitioner_type'] ??= $practitioner_model->getMorphClass();
        $practitioner = $this->practitionerEvaluation()->firstOrCreate([
            'visit_examination_id' => $attributes['visit_examination_id'],
            'practitioner_type'    => $attributes['practitioner_type'],
            'practitioner_id'      => $practitioner_model->getKey()
        ], [
            'is_commit'            => Commit::DRAFT->value,
            'role_as'              => $attributes['role_as'] ?? PIC::IS_OTHER->value,
            'name'                 => $practitioner_model->name ?? ''
        ]);
        return static::$practitioner_evaluation = $practitioner;
    }

    public function prepareCommitPractitionerEvaluation(?array $attributes = null): Model
    {
        $attributes ??= request()->all();
        $practitioner_evaluation = $this->prepareStorePractitionerEvaluation($attributes);
        $practitioner = $practitioner_evaluation;
        $practitioner->is_commit = Commit::COMMIT->value;
        $practitioner->save();

        //CHECKING ALL PRACTITIONERS
        $commit_now = $this->PractitionerEvaluationModel()->where('visit_examination_id', $practitioner->visit_examination_id)
            ->whereNot('id', $practitioner_evaluation->getKey())
            ->whereNot('is_commit', Commit::COMMIT->value)->count();
        $commit_now = ($commit_now > 0) ? false : true;
        if ($commit_now) {
            $visit_examination_schema = $this->schemaContract('visit_examination');
            $visit_examination_schema->prepareCommitVisitExamination([
                'visit_examination_id' => $practitioner->visit_examination_id
            ]);
        }
        return $practitioner;
    }

    public function commitPractitionerEvaluation(): array
    {
        return $this->transaction(function () {
            return $this->showPractitionerEvaluation($this->prepareCommitPractitionerEvaluation());
        });
    }

    public function storePractitionerEvaluation(): array
    {
        return $this->transaction(function () {
            return $this->showPractitionerEvaluation($this->prepareStorePractitionerEvaluation());
        });
    }

    public function showPractitionerEvaluation(?Model $model = null): array
    {
        $this->booting();
        $model ??= $this->getPractitionerEvaluation();
        if (!isset($model)) {
            $id = request()->id;
            if (!request()->has('id')) throw new \Exception('No ID provided', 422);
            $model = $this->PractitionerEvaluationModel()
                ->with($this->showUsingRelation())
                ->find($id);
        } else {
            $model->load($this->showUsingRelation());
        }
        return $this->transforming($this->__resources['show'], function () use ($model) {
            return $model ?? [];
        });
    }

    public function prepareViewPractitionerList(?array $attributes = null): Collection
    {
        $attributes ??= request()->all();
        return $this->practitionerEvaluation(function ($q) use ($attributes) {
            $q->when(isset($attributes['visit_examination_id']), function ($q) use ($attributes) {
                $q->where('visit_examination_id', $attributes['visit_examination_id']);
            });
        })->get();
    }

    public function viewPractitionerList(): array
    {
        return $this->transforming($this->__resources['view'], function () {
            return $this->prepareViewPractitionerList();
        });
    }

    public function prepareRemovePractitionerEvaluation(?array $attributes = null): bool
    {
        $attributes ??= request()->all();
        $practitioner = $this->practitionerEvaluation()->find($attributes['id']);
        return $practitioner->delete();
    }

    public function removePractitionerEvaluation(): bool
    {
        return $this->transaction(function () {
            return $this->prepareRemovePractitionerEvaluation();
        });
    }

    public function addOrChange(?array $attributes = []): self
    {
        $this->updateOrCreate($attributes);
        return $this;
    }
}
