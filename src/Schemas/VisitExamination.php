<?php

namespace Hanafalah\ModulePatient\Schemas;

use Illuminate\Database\Eloquent\{
    Model
};
use Hanafalah\ModuleMedicService\Enums\Label;
use Hanafalah\ModulePatient\{
    Enums\VisitExamination\CommitStatus,
    Enums\VisitExamination\ExaminationStatus,
    Enums\VisitPatient\VisitStatus,
    Enums\VisitRegistration\Status,
    ModulePatient,
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

    public function prepareStoreVisitExamination(VisitExaminationData $visit_examination_dto): Model{
        $visit_patient_model = $visit_examination_dto?->visit_patient_model ?? $this->VisitPatientModel()->findOrFail($visit_examination_dto->visit_patient_id);
        $add = [
            'visit_registration_id' => $visit_examination_dto->visit_registration_id,
            'visit_patient_id'      => $visit_examination_dto->visit_patient_id,
            'patient_id'            => $visit_examination_dto->patient_id ?? $visit_patient_model->patient_id,
            'sign_off_at'           => $visit_examination_dto->sign_off_at,
            'is_addendum'           => $visit_examination_dto->is_addendum ?? false,
        ];

        if (isset($visit_examination_dto->id)){
            $visit_examination = $this->VisitExaminationModel()->findOrFail($visit_examination_dto->id);
            $add['sign_off_at'] ??= $visit_examination->sign_off_at;
            $guard = ['id' => $visit_examination_dto->id];
            $create = [$guard,$add];
        }else{
            // $create = [$add];
            $create = [['id' => null],$add];
            $visit_registration_model = $visit_examination_dto->visit_registration_model ?? $this->VisitRegistrationModel()->findOrFail($visit_examination_dto->visit_registration_id);
            switch (true) {
                case $visit_examination_dto->sign_off:
                    $visit_registration_model->status ??= Status::COMPLETED->value;
                break;
                case $visit_examination_dto->is_addendum:
                break;
                default:
                    $visit_registration_model->status ??= Status::DRAFT->value;
                break;
            }
            $visit_registration_model->status = Status::PROCESSING->value;
            $visit_registration_model->save();
        }

        $visit_examination  = $this->usingEntity()->updateOrCreate(...$create);
        if (isset($visit_examination_dto->patient)){
            $patient_dto = &$visit_examination_dto->patient;
            $this->schemaContract('patient')->prepareStorePatient($patient_dto);
        }
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

        if (isset($visit_examination_dto->model_has_monitorings) && count($visit_examination_dto->model_has_monitorings) > 0){
            foreach ($visit_examination_dto->model_has_monitorings as &$model_has_monitoring_dto){
                $model_has_monitoring_dto->reference_type = $visit_examination->getMorphClass();
                $model_has_monitoring_dto->reference_id = $visit_examination->getKey();
                if (isset($model_has_monitoring_dto->monitoring)){
                    $monitoring_dto = &$model_has_monitoring_dto->monitoring;
                    $monitoring_dto->reference_type = 'Patient';
                    $monitoring_dto->reference_id = $visit_examination->patient_id;
                }
                $this->schemaContract('model_has_monitoring')->prepareStoreModelHasMonitoring($model_has_monitoring_dto);
            }
        }else{
            $this->ModelHasMonitoringModel()
                ->where('reference_type', $visit_examination->getMorphClass())
                ->where('reference_id',$visit_examination->getKey())
                ->delete();
        }
        
        $visit_examination_dto->sign_off_at ??= $visit_examination->sign_off_at;
        if (isset($visit_examination_dto->sign_off_at) && isset($visit_examination_dto->sign_off)){
            // $visit_registration_model = $visit_examination_dto->visit_registration_model ?? $visit_examination->visitRegistration;
            // $medic_service = $visit_registration_model->medicService;
            // if (!in_array($medic_service->label,['RAWAT INAP','VK'])){
            //     $visit_registration_model->status = Status::COMPLETED->value;
            //     $visit_registration_model->save();
            //     $visit_examination->setRelation('visitRegistration',$visit_registration_model);
            //     $visit_examination_dto->visit_registration_model = $visit_registration_model;
            // }
            $this->prepareVisitExaminationSignOff($visit_examination, $visit_examination_dto);        
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

    public function prepareVisitExaminationSignOff(Model &$visit_examination_model, VisitExaminationData &$visit_examination_dto): Model{
        $visit_examination = $visit_examination_dto->visit_examination_model ?? $this->VisitExaminationModel()->findOrFail($visit_examination_dto->id);
        $visit_examination->sign_off_at ??= $visit_examination_dto->sign_off_at;
        $visit_examination->save();
        $visit_examination->pushActivity(Activity::VISITATION->value, [
            ActivityStatus::VISITED->value
        ]);

        $visit_registration_model = $this->schemaContract('visit_registration')->prepareUpdateVisitRegistration($this->requestDTO(config('app.contracts.UpdateVisitRegistrationData'), [
            'id'     => $visit_examination->visit_registration_id,
            'visit_registration_model' => $visit_examination_dto->visit_registration_model ?? null,
            'status' => \Hanafalah\ModulePatient\Enums\VisitRegistration\Status::COMPLETED->value
        ]));

        if ($visit_registration_model->medicService->label == Label::PUSKESMAS_PEMBANTU){
            // request()->merge([
            //     'reference_type' => 'VisitExamination',
            //     'visit_examination_model' => $visit_examination,
            // ]);        
            // $this->schemaContract('pharmacy_sale')->prepareStorePharmacySale($this->requestDTO([

            // ]));
        }

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
