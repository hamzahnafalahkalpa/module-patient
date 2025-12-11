<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModuleMedicService\Enums\Label;
use Illuminate\Database\Eloquent\{
    Model
};

use Hanafalah\ModulePatient\{
    Contracts\Schemas\VisitRegistration as ContractsVisitRegistration,
    Enums\VisitRegistration\Status,
    ModulePatient
};
use Hanafalah\ModulePatient\Contracts\Data\UpdateVisitRegistrationData;
use Hanafalah\ModulePatient\Contracts\Data\VisitRegistrationData;
use Hanafalah\ModulePatient\Enums\{
    VisitRegistration\Activity as VisitRegistrationActivity,
    VisitRegistration\ActivityStatus as VisitRegistrationActivityStatus
};

class VisitRegistration extends ModulePatient implements ContractsVisitRegistration
{
    protected string $__entity = 'VisitRegistration';
    public $visit_registration_model;

    protected array $__cache = [
        'index' => [
            'name'     => 'visit_registration',
            'tags'     => ['visit_registration', 'visit_registration-index'],
            'duration'  => 24*1
        ],
        'show' => [
            'name'     => 'visit_registration-show',
            'tags'     => ['visit_registration', 'visit_registration-show'],
            'duration'  => 24*1
        ]
    ];

    public function prepareStoreVisitRegistration(VisitRegistrationData $visit_registration_dto): Model{
        if (isset($visit_registration_dto->visit_patient)){
            $visit_patient = $this->schemaContract('visit_patient')->prepareStoreVisitPatient($visit_registration_dto->visit_patient);
            $visit_registration_dto->visit_patient_id    = $visit_patient->getKey();
            $visit_registration_dto->visit_patient_type  = $visit_patient->getMorphClass();
            $visit_registration_dto->visit_patient_model = $visit_patient;
        }
        $visit_registration   = $this->createVisitRegistration($visit_registration_dto);
        $visit_patient      ??= $visit_registration_dto->visit_patient_model ?? $visit_registration->visitPatient;
        $visit_registration_dto->visit_patient_model ??= $visit_patient;
        if (isset($visit_registration_dto->visit_examination) && isset($visit_registration_dto->visit_patient_id)){
            $visit_examination_dto = &$visit_registration_dto->visit_examination;
            $visit_examination_dto->visit_patient_id         = $visit_patient->getKey();
            $visit_examination_dto->visit_registration_id    = $visit_registration->getKey();
            $visit_examination_dto->visit_registration_model = $visit_registration;
            $visit_examination_dto->visit_patient_model      = $visit_patient;
            $visit_examination_dto->patient_model            = $visit_patient->patient;
            $visit_examination = $this->schemaContract('visit_examination')->prepareStoreVisitExamination($visit_examination_dto);
            $visit_registration->setRelation('visitExamination', $visit_examination);
            $visit_registration_dto->props->props['prop_visit_examination'] = $visit_examination->toViewApiExcepts('visit_registration');
        }
        $this->storeItemRent($visit_registration_dto, $visit_registration);

        $this->fillingProps($visit_registration, $visit_registration_dto->props);
        $visit_registration->save();

        if (!isset($visit_registration_dto->id) && isset($visit_registration_dto->visit_patient_id)){
            if (in_array($visit_registration->prop_medic_service['label'], [
                Label::OUTPATIENT->value, Label::MCU->value,
                Label::LABORATORY->value, Label::RADIOLOGY->value
            ])) {
                $visit_registration->pushActivity(VisitRegistrationActivity::POLI_EXAM->value, [VisitRegistrationActivityStatus::POLI_EXAM_QUEUE->value]);
                $this->schemaContract('visit_patient')->preparePushLifeCycleActivity($visit_patient, $visit_registration, 'POLI_EXAM', [
                    'POLI_EXAM_QUEUE' => 'Pasien dalam antrian ke poli '.$visit_registration->prop_medic_service['name']
                ]);
            }
        }
        
        return $this->visit_registration_model = $visit_registration;
    }

    public function prepareUpdateVisitRegistration(UpdateVisitRegistrationData $update_visit_registration_dto): Model{
        $visit_registration_dto = &$update_visit_registration_dto;
        $visit_registration_model = $update_visit_registration_dto->visit_registration_model ?? $this->VisitRegistrationModel()->findOrFail($update_visit_registration_dto->id);
        $medic_service = $visit_registration_model->medicService;
        if (!in_array($medic_service->label, [Label::INPATIENT->value, Label::VERLOS_KAMER->value])){
            $visit_registration_model->status = $visit_registration_dto->status;
            $visit_registration_model->pushActivity(VisitRegistrationActivity::POLI_EXAM->value, [VisitRegistrationActivityStatus::POLI_EXAM_END->value]);
        }
        $this->fillingProps($visit_registration_model,$update_visit_registration_dto->props);
        $visit_registration_model->save();
        return $this->entityData($visit_registration_model);
    }

    protected function storeItemRent(VisitRegistrationData $visit_registration_dto, ?Model $visit_registration = null): self{
        if (config('module-patient.features.item_rent')) {
            $visit_registration ??= $visit_registration_dto->visit_registration_model;
            if (isset($visit_registration_dto->item_rents) && count($visit_registration_dto->item_rents) > 0){
                foreach ($visit_registration_dto->item_rents as $item_rent) {
                    $item_rent->reference_id = $visit_registration->getKey();
                    $item_rent->reference_type = $visit_registration->getMorphClass();
                    $item_rent->reference_model = $visit_registration;
                    $this->schemaContract('item_rent')->prepareStoreItemRent($item_rent);
                }
            }else{
                $visit_registration->itemRents()->delete();
            }
        }
        return $this;
    }

    public function createVisitRegistration(VisitRegistrationData &$visit_registration_dto): Model{
        $add = [
            'visited_at' => $visit_registration_dto->visited_at ?? now(),
            'visit_patient_id'   => $visit_registration_dto->visit_patient_id,
            'visit_patient_type' => $visit_registration_dto->visit_patient_type,
            'warehouse_type'    => $visit_registration_dto->warehouse_type ?? null,
            'warehouse_id'      => $visit_registration_dto->warehouse_id ?? null,
        ];
        if (isset($visit_registration_dto->id)){
            $guard = ['id' => $visit_registration_dto->id];
        }else{
            $add['parent_id'] = $visit_registration_dto->parent_id ?? null; 
            $guard = [
                'id'                 => null,
                'medic_service_id'   => $visit_registration_dto->medic_service_id,
                'referral_id'        => $visit_registration_dto->referral_id
            ];
        }
        if (isset($visit_registration_dto->status)){
            $add['status'] = $visit_registration_dto->status;
        }
        $visit_registration = $this->usingEntity()->updateOrCreate($guard,$add);
        $this->initTransaction($visit_registration_dto, $visit_registration);

        if (isset($visit_registration_dto->referral_model)){
            $referral_view = $visit_registration_dto->referral_model->toViewApi()->resolve();
            $visit_registration_dto->props->props['prop_referral'] = array_intersect_key($referral_view, array_flip([
                'id', 'medic_service', 'visit', 'status', 'created_at', 'updated_at'
            ]));
        }
        $visit_registration->load(['paymentSummary', 'transaction']);
        
        if (isset($visit_registration->visitPatient)){
            $visit_patient_model = ($visit_registration_dto->visit_patient_model ??= $visit_registration->visitPatient);
            $visit_patient_model->load('paymentSummary');
            if (!isset($visit_registration_dto->props->props['prop_visit_patient'])){
                $visit_registration_dto->props->props['prop_visit_patient'] = $visit_registration_dto->visit_patient_model->toViewApi()->resolve();
            }
            $visit_registration_dto->payment_summary->parent_id ??= $visit_patient_model->paymentSummary->getKey();
            $this->initPaymentSummary($visit_registration_dto, $visit_registration);
        }

        if (isset($visit_registration_dto->practitioner_evaluations)){
            foreach ($visit_registration_dto->practitioner_evaluations as &$practitioner_evaluation) {
                $this->initPractitionerEvaluation($practitioner_evaluation, $visit_registration);
            }
        }
        $this->fillingProps($visit_registration, $visit_registration_dto->props);
        $visit_registration->save();
        return $this->visit_registration_model = $visit_registration;
    }

    public function visitRegistrationCancellation(?array $attributes): Model{
        $attributes ??= request()->all();
        $visitRegistration         = $this->VisitRegistrationModel()->find($attributes['visit_registration_id']);
        $visitRegistration->status = Status::CANCELLED->value;
        $visitRegistration->save();

        $visitRegistration->pushActivity(VisitRegistrationActivity::POLI_SESSION->value, [VisitRegistrationActivityStatus::POLI_SESSION_CANCEL->value]);

        return $visitRegistration;
    }
}
