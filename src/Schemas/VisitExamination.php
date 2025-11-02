<?php

namespace Hanafalah\ModulePatient\Schemas;

use Illuminate\Database\Eloquent\{
    Model
};
use Hanafalah\ModulePatient\{
    Enums\VisitExamination\CommitStatus,
    Enums\VisitExamination\ExaminationStatus,
    Enums\VisitPatient\VisitStatus,
    Enums\VisitRegistration\Status,
    ModulePatient,
    Enums\EvaluationEmployee\Commit,
    Enums\VisitExamination\Activity,
    Enums\VisitExamination\ActivityStatus,
    Contracts\Schemas\VisitExamination as ContractsVisitExamination
};
use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationData;
use Hanafalah\ModulePatient\Enums\{
    VisitRegistration\Activity as VisitRegistrationActivity,
    VisitRegistration\ActivityStatus as VisitRegistrationActivityStatus
};

class VisitExamination extends ModulePatient implements ContractsVisitExamination
{
    protected string $__entity = 'VisitExamination';
    protected mixed $__order_by_created_at = 'desc'; //asc, desc, false
    public $visit_examination_model;

    public function prepareCommitVisitExamination(?array $attributes = null): Model{
        $attributes ??= request()->all();

        $visit_examination = $this->VisitExaminationModel()->find($attributes['visit_examination_id']);
        $visit_examination->is_commit = Commit::COMMIT->value;
        $visit_examination->save();

        //PUSH ACTIVITY FOR COMMITED
        $visit_examination->pushActivity(Activity::VISITATION->value, [ActivityStatus::VISITED->value]);

        $assessments = $visit_examination->assessments()->whereIn('morph', ['InitialDiagnose', 'SecondaryDiagnose', 'PrimaryDiagnose'])->get();
        foreach ($assessments as $assessment) {
            $assessment = $this->{$assessment->morph . 'Model'}()->find($assessment->getKey());
            $assessment->reporting();
        }
        return $visit_examination;
    }

    public function commitVisitExamination(): array{
        return $this->transaction(function () {
            return $this->showVisitExamination($this->prepareCommitVisitExamination());
        });
    }

    public function prepareStoreVisitExamination(VisitExaminationData $visit_examination_dto): Model{
        $visit_patient_model = $visit_examination_dto?->visit_patient_model ?? $this->VisitPatientModel()->findOrFail($visit_examination_dto->visit_patient_id);
        $add = [
            'visit_registration_id' => $visit_examination_dto->visit_registration_id,
            'visit_patient_id'      => $visit_examination_dto->visit_patient_id,
            'patient_id'            => $visit_patient_model->patient_id,
            'sign_off_at'           => $visit_examination_dto->sign_off_at,
            'is_addendum'           => $visit_examination_dto->is_addendum ?? false,
        ];

        if (isset($visit_examination_dto->id)){
            $guard = ['id' => $visit_examination_dto->id];
            $create = [$guard,$add];
        }else{
            $create = [$add];
        }

        $visit_examination  = $this->usingEntity()->updateOrCreate(...$create);
        $visit_examination_dto->visit_examination_model = &$visit_examination;
        if (!isset($visit_examination_dto->id)){
            $visit_examination->pushActivity(Activity::VISITATION->value, [
                ActivityStatus::VISIT_CREATED->value, 
                ActivityStatus::VISITING->value
            ]);
        }

        if (isset($visit_examination_dto->practitioner_evaluations)) {
            foreach ($visit_examination_dto->practitioner_evaluations as &$practitioner_evaluation) {
                $this->initPractitionerEvaluation($practitioner_evaluation, $visit_examination);
            }
        }

        //SET ASSESSMENT
        if (isset($visit_examination_dto->examination)){            
            $examination_dto = &$visit_examination_dto->examination;
            $examination_dto->visit_examination_id ??= $visit_examination->getKey();
            $examination_dto->visit_examination_model = $visit_examination;            
            $examination_dto->visit_patient_model ??= $visit_examination_dto->visit_patient_model;
            $examination_dto->visit_registration_model ??= $visit_examination_dto->visit_registration_model;
            $examination_dto->patient_model ??= $visit_examination_dto->patient_model;
            if (!isset($examination_dto->id)){
                $examination_dto->in_view_response = true;
                $response = $this->schemaContract('examination')->prepareStoreExamination($examination_dto);
                $visit_examination_dto->props->props['examination'] = $response;
            }else{
                $this->schemaContract('examination')->prepareStoreExamination($examination_dto);
            }
            $visit_examination_dto->is_addendum = false;
            $visit_examination->is_addendum = false;
        }
        $visit_examination_dto->props->props['sign_off_at'] ??= $visit_examination->sign_off_at;
        if ($visit_examination_dto->props->props['sign_off_at']){
            $this->prepareVisitExaminationSignOff($visit_examination_dto);        
        }
        
        // if (in_array($medic_service->flag, [Label::OUTPATIENT->value, Label::MCU->value])) {
            //ADD DEFAULT SCREENING
            // $screenings = [];
            // $screening_models = $this->ScreeningModel()->whereHas('hasServices', function ($query) use ($medic_service) {
            //     $query->where('service_id', $medic_service->service->getKey());
            // })->get();
            // if (isset($screening_models) && count($screening_models) > 0) {
            //     foreach ($screening_models as $screening) {
            //         $screenings[] = [
            //             $screening->getKeyName() => $screening->getKey(),
            //             'name'                   => $screening->name
            //         ];
            //     }
            //     $visit_examination->setAttribute('screenings', $screenings);
            //     $visit_examination->save();
            // }
        // }

        $this->fillingProps($visit_examination, $visit_examination_dto->props);
        $visit_examination->save();
        return $this->visit_examination_model = $visit_examination;
    }

    public function prepareVisitExaminationSignOff(VisitExaminationData $visit_examination_dto): Model{
        $visit_examination = $visit_examination_dto->visit_examination_model ?? $this->VisitExaminationModel()->findOrFail($visit_examination_dto->id);
        $visit_examination->sign_off_at = $visit_examination_dto->props->props['sign_off_at'];
        $visit_examination->save();
        $visit_examination->pushActivity(Activity::VISITATION->value, [
            ActivityStatus::VISITED->value
        ]);

        $this->schemaContract('visit_registration')->prepareUpdateVisitRegistration($this->requestDTO(config('app.contracts.UpdateVisitRegistrationData'), [
            'id'     => $visit_examination->visit_registration_id,
            'visit_registration_model' => $visit_examination_dto->visit_registration_model ?? null,
            'status' => \Hanafalah\ModulePatient\Enums\VisitRegistration\Status::PROCESSING->value
        ]));
        return $visit_examination;
    }

    public function visitExaminationCancelation(?array $attributes = null){
        $attributes ??= request()->all();
        $visit_examination = $this->prepareShowVisitExamination([
            "id" => $attributes['visit_examination_id']
        ]);

        if (!isset($visit_examination)) throw new \Exception("Data Examination Tidak Di Temukan");

        // CANCELLATION VISIT EXAMINATION
        $visit_examination->status = ExaminationStatus::CANCELLED->value;
        $visit_examination->save();
        $visit_examination->pushActivity(Activity::VISITATION->value, [ActivityStatus::CANCELLED->value]);

        $visit_registration = $visit_examination->visitRegistration;
        if (!isset($visit_registration)) throw new \Exception("Data Visit Registration Tidak Di Temukan");

        $visit_registration = $this->schemaContract('visit_registration')->visitRegistrationCancellation([
            "visit_registration_id" => $visit_registration->getKey()
        ]);

        $visit_patient = $visit_registration->visitPatient;
        if (!isset($visit_patient)) throw new \Exception("Data Visit Patient Tidak Ditemukan");

        $visit_patient->load([
            "visitRegistrations" => fn($q) => $q->whereIn("status", [
                Status::PROCESSING->value,
                Status::DRAFT->value
            ])
        ]);

        if (empty($visit_patient->visitRegistrations)) {
            $visit_patient->status = VisitStatus::CANCELLED->value;
            $visit_patient->saveQuietly();
        }

        return $visit_patient;
    }

    public function visitExaminationDoneProcess(?array $attributes = null)
    {
        $attributes ??= request()->all();
        $visit_examination = $this->prepareShowVisitExamination([
            "id" => $attributes['visit_examination_id']
        ]);
        if (isset($visit_examination)) {
            $visit_examination->is_commit = CommitStatus::COMMITED->value;
            $visit_examination->save();

            if ($visit_examination->is_commit == CommitStatus::COMMITED->value) {
                $visit_registration = $visit_examination->visitRegistration;

                if (isset($visit_registration)) {
                    $visit_registration->status = Status::COMPLETED->value;
                    $visit_registration->save();

                    $visit_registration->pushActivity(VisitRegistrationActivity::POLI_SESSION->value, [VisitRegistrationActivityStatus::POLI_SESSION_END->value]);
                }

                $visit_examination->pushActivity(Activity::VISITATION->value, [ActivityStatus::VISITED->value]);
                $visit_examination->reported_at = now();
                $visit_examination->status = ExaminationStatus::VISITED->value;
                $visit_examination->save();

                $visit_examination->pushActivity(Activity::VISITATION->value, [ActivityStatus::VISITED->value]);

                return $visit_examination;
            } else {
                throw new \Exception("Harap Commit terlebih dahulu sebelum penyelesaian patient!");
            }
        } else {
            throw new \Exception("Visit Examination Not Found");
        }
    }
}
