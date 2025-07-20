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
        if (!isset($practitioner_evaluation_dto->vsiit_registration_id)) throw new \Exception('visit_registration_id is required', 422);
        if (!isset($practitioner_evaluation_dto->visit_examination_id)) throw new \Exception('visit_examination_id is required', 422);
        if (!isset($practitioner_evaluation_dto->practitioner_id))      throw new \Exception('practitioner_id is required', 422);

        $practitioner_model = app(config('module-patient.practitioner'))->findOrFail($practitioner_evaluation_dto->practitioner_id);
        $profession_model   = $practitioner_model->profession;

        $practitioner = $this->practitionerEvaluation()->firstOrCreate([
            'visit_registration_id' => $practitioner_evaluation_dto->visit_registration_id,
            'visit_examination_id' => $practitioner_evaluation_dto->visit_examination_id,
            'practitioner_type'    => $practitioner_model->getMorphClass(),
            'practitioner_id'      => $practitioner_model->getKey()
        ], [
            'is_commit'            => Commit::DRAFT->value,
            'profession_id'        => $profession_model?->getKey() ?? null,
            'name'                 => $practitioner_model?->name ?? ''
        ]);

        $props = &$practitioner_evaluation_dto->props;
        $props['prop_practitioner'] = $practitioner_model->toViewApi()->resolve();
        $props['prop_profession']   = $profession_model?->toViewApi()->resolve();

        $this->fillingProps($practitioner, $practitioner_evaluation_dto);
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
