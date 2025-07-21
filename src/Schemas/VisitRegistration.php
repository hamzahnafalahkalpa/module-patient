<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModuleMedicService\Enums\Label;
use Illuminate\Database\Eloquent\{
    Model
};

use Hanafalah\ModulePatient\{
    Contracts\Schemas\VisitRegistration as ContractsVisitRegistration,
    Enums\VisitRegistration\RegistrationStatus,
    ModulePatient
};
use Hanafalah\ModulePatient\Contracts\Data\VisitRegistrationData;
use Hanafalah\ModulePatient\Enums\{
    VisitRegistration\Activity as VisitRegistrationActivity,
    VisitRegistration\ActivityStatus as VisitRegistrationActivityStatus
};
use Laravel\Octane\Facades\Octane;

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
            $visit_registration_dto->visit_patient_id = $visit_patient->getKey();
            $visit_registration_dto->visit_patient_model = $visit_patient;
        }

        $visit_registration   = $this->createVisitRegistration($visit_registration_dto);
        $visit_patient      ??= $visit_registration_dto->visit_patient_model;

        if (isset($visit_registration_dto->visit_examination)){
            $visit_examination_dto = &$visit_registration_dto->visit_examination;
            $visit_examination_dto->visit_patient_id      = $visit_patient->getKey();
            $visit_examination_dto->visit_registration_id = $visit_registration->getKey();
            $visit_examination = $this->schemaContract('visit_examination')->prepareStoreVisitExamination($visit_examination_dto);
            $visit_registration_dto->props->props['prop_visit_examination'] = $visit_examination->toViewApi()->resolve();
        }

        $this->fillingProps($visit_registration, $visit_registration_dto->props);
        $visit_registration->save();

        if (in_array($visit_registration->prop_medic_service['label'], [
            Label::OUTPATIENT->value, Label::MCU->value,
            Label::LABORATORY->value, Label::RADIOLOGY->value
        ])) {
            $visit_registration->pushActivity(VisitRegistrationActivity::POLI_EXAM->value, [VisitRegistrationActivityStatus::POLI_EXAM_QUEUE->value]);
            $this->schemaContract('visit_patient')->preparePushLifeCycleActivity($visit_patient, $visit_registration, 'POLI_EXAM', [
                'POLI_EXAM_QUEUE' => 'Pasien dalam antrian ke poli '.$visit_registration->prop_medic_service['name']
            ]);
        }
        dd($visit_registration);

        return $this->visit_registration_model = $visit_registration;
    }

    public function createVisitRegistration(VisitRegistrationData &$visit_registration_dto): Model{
        $add = [
            'visited_at'        => now(),
            'parent_id'         => $visit_registration_dto->parent_id ?? null
        ];

        $guard = [
            'id'                 => $visit_registration_dto->id ?? null,
            'visit_patient_id'   => $visit_registration_dto->visit_patient_id,
            'visit_patient_type' => $visit_registration_dto->visit_patient_type,
            'medic_service_id'   => $visit_registration_dto->medic_service_id
        ];

        $visit_registration = $this->usingEntity()->updateOrCreate($guard,$add);
        $visit_registration->load(['paymentSummary', 'transaction']);
        
        $this->initTransaction($visit_registration_dto, $visit_registration)
             ->initPaymentSummary($visit_registration_dto, $visit_registration);

        $this->fillingProps($visit_registration, $visit_registration_dto->props);
        $visit_registration->save();
        return $this->visit_registration_model = $visit_registration;
    }

    public function visitRegistrationCancellation(?array $attributes): Model{
        $attributes ??= request()->all();
        $visitRegistration         = $this->VisitRegistrationModel()->find($attributes['visit_registration_id']);
        $visitRegistration->status = RegistrationStatus::CANCELLED->value;
        $visitRegistration->save();

        $visitRegistration->pushActivity(VisitRegistrationActivity::POLI_SESSION->value, [VisitRegistrationActivityStatus::POLI_SESSION_CANCEL->value]);

        return $visitRegistration;
    }
}
