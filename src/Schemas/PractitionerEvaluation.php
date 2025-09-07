<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModulePatient\Contracts\Data\PractitionerEvaluationData;
use Illuminate\Database\Eloquent\{
    Collection,
    Model
};
use Hanafalah\ModulePatient\Contracts\Schemas\{
    PractitionerEvaluation as ContractsPractitionerEvaluation
};
use Hanafalah\ModulePatient\Enums\{
    EvaluationEmployee\Commit,
};
use Hanafalah\ModulePatient\ModulePatient;

class PractitionerEvaluation extends ModulePatient implements ContractsPractitionerEvaluation
{
    protected string $__entity = 'PractitionerEvaluation';
    public $practitioner_evaluation;
    
    public function prepareStorePractitionerEvaluation(PractitionerEvaluationData $practitioner_evaluation_dto): Model{
        $practitioner_model = app(config('database.models.'.config('module-patient.practitioner')))
                                ->findOrFail($practitioner_evaluation_dto->practitioner_id);
        $profession_model   = $practitioner_model->profession;

        $practitioner = $this->usingEntity()->firstOrCreate([
            'reference_type'        => $practitioner_evaluation_dto->reference_type,
            'reference_id'          => $practitioner_evaluation_dto->reference_id,
            'practitioner_type'     => $practitioner_model->getMorphClass(),
            'practitioner_id'       => $practitioner_model->getKey()
        ], [
            'is_commit'             => Commit::DRAFT->value,
            'profession_id'         => $profession_model?->getKey() ?? null,
            'name'                  => $practitioner_model?->name ?? ''
        ]);
        $props = &$practitioner_evaluation_dto->props;
        $props['prop_practitioner'] = $practitioner_model->toViewApiOnlies('id','name','flag','label');
        if (!$practitioner->exists) $props['prop_profession'] = $profession_model?->toViewApiOnlies('id','name','flag','label');

        $this->fillingProps($practitioner, $practitioner_evaluation_dto->props);
        $practitioner->save();
        return $this->practitioner_evaluation = $practitioner;
    }

    public function prepareCommitPractitionerEvaluation(?array $attributes = null): Model{
        $attributes ??= request()->all();
        $practitioner            = $this->usingEntity()->findOrFail($attributes['id']);
        $practitioner->is_commit = Commit::COMMIT->value;
        $practitioner->save();

        //CHECKING ALL PRACTITIONERS
        $commit_now = $this->PractitionerEvaluationModel()->where('visit_examination_id', $practitioner->visit_examination_id)
            ->whereNot('id', $practitioner->getKey())
            ->whereNot('is_commit', Commit::COMMIT->value)->count();
        $commit_now = ($commit_now > 0) ? false : true;
        if ($commit_now) {
            $this->schemaContract('visit_examination')->prepareCommitVisitExamination([
                'visit_examination_id' => $practitioner->visit_examination_id
            ]);
        }
        return $practitioner;
    }

    public function commitPractitionerEvaluation(): array{
        return $this->transaction(function () {
            return $this->showPractitionerEvaluation($this->prepareCommitPractitionerEvaluation());
        });
    }


}
