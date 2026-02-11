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
    public $is_recently_created = false;
    public $is_sign_off = false;

    public function prepareStoreVisitExamination(VisitExaminationData $visit_examination_dto): Model{
        $visit_patient_model = $visit_examination_dto?->visit_patient_model ?? $this->VisitPatientModel()->findOrFail($visit_examination_dto->visit_patient_id);
        $add = [
            'visit_registration_id' => $visit_examination_dto->visit_registration_id,
            'visit_patient_id'      => $visit_examination_dto->visit_patient_id,
            'patient_id'            => $visit_examination_dto->patient_id ?? $visit_patient_model->patient_id,
            'is_addendum'           => $visit_examination_dto->is_addendum ?? false,
        ];
        if (isset($visit_examination_dto->sign_off_at)){
            $add['sign_off_at'] ??= $visit_examination_dto->sign_off_at;
        }
        if (isset($visit_examination_dto->id)){
            $visit_examination = $this->VisitExaminationModel()->findOrFail($visit_examination_dto->id);
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
            if (isset($visit_examination_dto->examination)){            
                $visit_registration_model->status = Status::PROCESSING->value;
            }
            $visit_registration_model->save();
        }

        $visit_examination  = $this->usingEntity()->updateOrCreate(...$create);
        if ($visit_examination->wasChanged('sign_off_at')){
            $this->is_sign_off = true;
        }
        $visit_examination_dto->visit_examination_model = &$visit_examination;
        if (!isset($visit_examination_dto->id)){
            $visit_examination->pushActivity(Activity::VISITATION->value, [
                ActivityStatus::VISIT_CREATED->value, 
                ActivityStatus::VISITING->value
            ]);
        }
        if ($visit_examination->wasRecentlyCreated) {
            $this->is_recently_created = true;
        }
        
        if (isset($visit_examination_dto->patient)){
            $patient_dto = &$visit_examination_dto->patient;
            $this->schemaContract('patient')->prepareStorePatient($patient_dto);
        }
        
        if (isset($visit_examination_dto->practitioner_evaluations)) {
            foreach ($visit_examination_dto->practitioner_evaluations as &$practitioner_evaluation) {
                $this->initPractitionerEvaluation($practitioner_evaluation, $visit_examination);
            }
        }
        
        $visit_registration_model = $visit_examination_dto->visit_registration_model ?? $visit_examination->visitRegistration;
        $visit_examination_dto->visit_registration_payment_summary_model ??= $visit_registration_model->paymentSummary;

        $visit_patient_model = $visit_examination_dto->visit_patient_model ?? $visit_examination->visitPatient;
        $visit_examination_dto->visit_patient_payment_summary_model ??= $visit_patient_model->paymentSummary;
        //SET ASSESSMENT
        if (isset($visit_examination_dto->examination)){       
            if ($visit_registration_model->status == 'DRAFT'){
                $visit_registration_model->status = Status::PROCESSING->value;
                $visit_registration_model->save();
            }

            $examination_dto = &$visit_examination_dto->examination;
            $examination_dto->visit_examination_id ??= $visit_examination->getKey();
            $examination_dto->visit_examination_model = $visit_examination;            
            $examination_dto->visit_patient_model ??= $visit_examination_dto->visit_patient_model;

            $examination_dto->visit_registration_model ??= $visit_examination_dto->visit_registration_model;
            $examination_dto->visit_registration_payment_summary_model ??= $visit_examination_dto->visit_registration_payment_summary_model;
            $examination_dto->visit_patient_payment_summary_model ??= $visit_examination_dto->visit_patient_payment_summary_model;
            $examination_dto->patient_model ??= $visit_examination_dto->patient_model;
            if (!isset($examination_dto->id)){
                $examination_dto->in_view_response = false;
                config([
                    'module-payment.setting.payment_detail.skip_event_created' => true
                ]);
                $response = $this->schemaContract('examination')->prepareStoreExamination($examination_dto);
                // $visit_examination_dto->props->props['examination'] = $response;

                $emr = config('module-examination.assessment.emr',[]);
                if (count($emr) > 0){
                    $visit_examination->load('examinationSummary');
                    $visit_exam_examination_summary = $visit_examination->examinationSummary;
                    if (isset($emr[$visit_exam_examination_summary->reference_id])){
                        $visit_exam_existing_emr = $visit_exam_examination_summary->emr;
                        foreach ($emr[$visit_exam_examination_summary->reference_id] as $key => $list_emr) {
                            $visit_exam_existing_emr[$key] = $list_emr;
                        }
                        $visit_exam_examination_summary->setAttribute('emr',$visit_exam_existing_emr);
                        $visit_exam_examination_summary->save();
                    }
                }
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
        $this->prepareVisitExaminationSignOff($visit_examination, $visit_examination_dto);                
        
        if ($this->is_recently_created){
            $this->afterVisitExaminationCreated($visit_examination,$visit_examination_dto);
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
        // if (isset($visit_examination_dto->sign_off_at) && isset($visit_examination_dto->sign_off)){
        if ($this->is_sign_off){
            // $visit_examination = $visit_examination_dto->visit_examination_model ?? $this->VisitExaminationModel()->findOrFail($visit_examination_dto->id);
            $visit_examination = &$visit_examination_model;
            $visit_examination->sign_off_at ??= $visit_examination_dto->sign_off_at;
            $visit_examination->save();
            $visit_examination->pushActivity(Activity::VISITATION->value, [
                ActivityStatus::VISITED->value
            ]);
            $visit_exam_resolve = $visit_examination_model;
            $visit_exam_resolve = $visit_exam_resolve->toShowApi()->resolve();
            
            $visit_registration_model = $visit_examination_dto->visit_registration_model ?? $visit_examination->visitRegistration;
            $visit_payment_summary = $visit_registration_model->paymentSummary;
    
            $visit_examination->load('treatments');
            $treatments = $visit_examination->treatments;
            $calculate_amount = 0;
            $calculate_cogs = 0;
            $calculate_discount = 0;
            foreach ($treatments as $treatment) {
                $exam = $treatment['exam'];
                $treatment_exam = $exam['treatment'];
                $qty = floatval($exam['qty'] ?? 1);
                $calculate_amount = $qty*intval($treatment_exam['price'] ?? 0);
                $calculate_cogs = $qty*intval($treatment_exam['cogs'] ?? 0);
                $calculate_discount = $qty*intval($treatment_exam['discount'] ?? 0);
        
                $visit_payment_summary->amount += $calculate_amount;
                $visit_payment_summary->debt += $calculate_amount;
                $visit_payment_summary->discount += $calculate_discount;
                $visit_payment_summary->cogs += $calculate_cogs;
            }
            $visit_payment_summary->save();

            $this->schemaContract('visit_registration')->prepareUpdateVisitRegistration($this->requestDTO(config('app.contracts.UpdateVisitRegistrationData'), [
                'id'     => $visit_examination->visit_registration_id,
                'visit_registration_model' => $visit_examination_dto->visit_registration_model ?? null,
                'status' => \Hanafalah\ModulePatient\Enums\VisitRegistration\Status::COMPLETED->value
            ]));
            $visit_registration = $visit_examination_dto->visit_registration_model ?? $this->VisitRegistrationModel()->findOrFail($visit_examination_model->visit_registration_id);
            $visit_patient_model = $visit_examination_dto->visit_patient_model ?? $this->VisitPatientModel()->findOrFail($visit_examination_model->visit_patient_id);
            $patient_model = $visit_examination_dto->patient_model ??= $this->PatientModel()->findOrFail($visit_examination_model->patient_id);
            $patient_summary_model = $this->schemaContract('patient_summary')->prepareStorePatientSummary($this->requestDTO(config('app.contracts.PatientSummaryData'),[
                'patient_id'      => $patient_model->getKey(),
                'patient_model'   => $patient_model,
                'reference_type'  => $patient_model->reference_type,
                'reference_id'    => $patient_model->reference_id,
                'reference_model' => $patient_model->reference,
                'last_visit'      => $visit_exam_resolve,
            ]));
    
            $visit_reg_summary_model = $this->schemaContract('examination_summary')->prepareStoreExaminationSummary($this->requestDTO(config('app.contracts.ExaminationSummaryData'),[
                'patient_id' => $patient_model->getKey(),
                'patient_model' => $patient_model,
                'reference_type' => $visit_registration->getMorphClass(),
                'reference_id' => $visit_registration->getKey(),
                'reference_model' => $visit_registration,
                'last_visit' => $visit_exam_resolve
            ]));
    
            $visit_patient_summary_model = $this->schemaContract('examination_summary')->prepareStoreExaminationSummary($this->requestDTO(config('app.contracts.ExaminationSummaryData'),[
                'patient_id' => $patient_model->getKey(),
                'patient_model' => $patient_model,
                'reference_type' => $visit_patient_model->getMorphClass(),
                'reference_id' => $visit_patient_model->getKey(),
                'reference_model' => $visit_patient_model,
                'last_visit' => $visit_exam_resolve
            ]));
    
            $visit_exam_examination_summary = $visit_examination_model->examinationSummary;
            if (isset($visit_exam_examination_summary->emr)){
                $patient_emr = $patient_summary_model->emr ?? [];
                $visit_reg_emr = $visit_reg_summary_model->emr ?? [];
                $visit_pat_emr = $visit_patient_summary_model->emr ?? [];

                // Mapping EMR keys to patient summary fields (array types with default empty array)
                $emr_to_summary_mapping = [
                    'Allergy' => 'allergies',
                    'Symptom' => 'symptoms',
                    'FamilyIllness' => 'family_illnesses',
                    'BasicPrescription' => 'medications',
                    'ClinicalTreatment' => 'treatments',
                    'LabTreatment' => 'treatments',
                    'RadiologyTreatment' => 'treatments',
                ];
                // Single object types with default empty array
                $single_object_mapping = [
                    'Anthropometry' => 'anthropometry',
                    'VitalSign' => 'vital_sign',
                ];

                // Initialize all patient summary fields with default values if not exists
                $array_fields = array_unique(array_values($emr_to_summary_mapping));
                foreach ($array_fields as $field) {
                    if (!isset($patient_summary_model->{$field})) {
                        $patient_summary_model->setAttribute($field, []);
                    }
                }
                foreach ($single_object_mapping as $field) {
                    if (!isset($patient_summary_model->{$field})) {
                        $patient_summary_model->setAttribute($field, null);
                    }
                }

                foreach ($visit_exam_examination_summary->emr as $key => $emr_data) {
                    // Check if emr_data is a 2D array (list of items) vs 1D array (single object)
                    $is_list = is_array($emr_data) && isset($emr_data[0]) && is_array($emr_data[0]);

                    if ($is_list) {
                        // For 2D arrays (list of items), unshift new data to front and limit to 10
                        $patient_emr[$key] = array_slice(array_merge($emr_data, $patient_emr[$key] ?? []), 0, 10);
                        $visit_reg_emr[$key] = array_slice(array_merge($emr_data, $visit_reg_emr[$key] ?? []), 0, 10);
                        $visit_pat_emr[$key] = array_slice(array_merge($emr_data, $visit_pat_emr[$key] ?? []), 0, 10);

                        // Set patient summary fields for array types
                        if (isset($emr_to_summary_mapping[$key])) {
                            $field = $emr_to_summary_mapping[$key];
                            $existing = $patient_summary_model->{$field} ?? [];
                            $exams = [];
                            foreach ($emr_data as $exam) {
                                $exams[] = $exam['exam'];
                            }
                            $merged = array_slice(array_merge($exams, $existing), 0, 10);
                            $patient_summary_model->setAttribute($field, $merged);
                        }
                    } else {
                        // For 1D arrays (single object), merge with current data to keep up-to-date
                        $patient_emr[$key] = array_merge($patient_emr[$key] ?? [], $emr_data);
                        $visit_reg_emr[$key] = array_merge($visit_reg_emr[$key] ?? [], $emr_data);
                        $visit_pat_emr[$key] = array_merge($visit_pat_emr[$key] ?? [], $emr_data);

                        // Set patient summary fields for single object types
                        if (isset($single_object_mapping[$key])) {
                            $field = $single_object_mapping[$key];
                            $existing = $patient_summary_model->{$field} ?? [];
                            $merged = array_merge($existing, $emr_data['exam']);
                            $patient_summary_model->setAttribute($field, $merged);
                        }
                    }
                }

                $patient_summary_model->setAttribute('emr', $patient_emr);
                $patient_summary_model->save();

                $visit_reg_summary_model->setAttribute('emr', $visit_reg_emr);
                $visit_reg_summary_model->save();

                $visit_patient_summary_model->setAttribute('emr', $visit_pat_emr);
                $visit_patient_summary_model->save();
            }
        }

        return $visit_examination_model;
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

    protected function afterVisitExaminationCreated(Model &$visit_examination_model, VisitExaminationData &$visit_examination_dto): self{
        return $this;
    }
}
