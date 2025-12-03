<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModulePatient\{
    Contracts\Schemas\Referral as ContractsReferral,
    ModulePatient
};
use Hanafalah\ModulePatient\Contracts\Data\ReferralData;
use Illuminate\Database\Eloquent\Model;

class Referral extends ModulePatient implements ContractsReferral
{
    protected string $__entity = 'Referral';
    public $referral_model;

    protected array $__cache = [
        'index' => [
            'name'     => 'referral',
            'tags'     => ['referral', 'referral-index'],
            'duration' => 60
        ]
    ];

    public function prepareStoreReferral(ReferralData $referral_dto): Model{
        $add = [
            'medic_service_id' => $referral_dto->medic_service_id,
            'status'           => $referral_dto->status,
            'visited_at'       => $referral_dto->visited_at ?? now()->format('Y-m-d')
        ];

        if (isset($referral_dto->id)){
            $guard = ['id' => $referral_dto->id];
        }else{
            $guard = [
                'visit_type'       => $referral_dto->visit_type, 
                'visit_id'         => $referral_dto->visit_id,
                'referral_type'    => $referral_dto->referral_type,
            ];
        }
        $create = [$guard, $add];

        $referral = $this->usingEntity()->updateOrCreate(...$create);
        if (isset($referral_dto->medic_service_id)){
            $referral_dto->props->props['prop_medic_service'] = $this->MedicServiceModel()->findOrFail($referral_dto->medic_service_id)->toViewApi()->resolve();
        }

        if (isset($referral_dto->visit_registration)){
            switch (true){
                case $referral_dto->visit_type == 'VisitRegistration':
                    $this->mapperForVisitRegistration($referral_dto,$referral);
                break;
            }            
            $visit_registration_dto = &$referral_dto->visit_registration;
            $visit_registration_dto->referral_id = $referral->getKey();
            $visit_registration_dto->referral_model = $referral;
            $visit_registration = $this->schemaContract('visit_registration')->prepareStoreVisitRegistration($visit_registration_dto);
            $referral_dto->props->props['prop_visit_registration'] = $visit_registration->toViewApi()->resolve();
            $referral_dto->visit_model = $visit_registration;
        }
        if (!isset($referral_dto->visit_model)) $referral_dto->visit_model = $this->{$referral_dto->visit_type.'Model'}()->findOrFail($referral_dto->visit_id);
        $referral_dto->props->props['prop_visit'] = $referral_dto->visit_model->toViewApi()->resolve();
        $this->fillingProps($referral,$referral_dto->props);
        $referral->save();
        return $this->referral_model = $referral;
    }

    protected function mapperForVisitRegistration(ReferralData &$referral_dto, Model $referral)
    {
        $timezone = config('app.client_timezone', 'Asia/Jakarta');
        $today = \Carbon\Carbon::now($timezone)->format('Y-m-d');
        $visit_registration_model = $this->VisitRegistrationModel()->with('visitPatient')->findOrFail($referral_dto->visit_id);
        $visit_registration_dto = &$referral_dto->visit_registration;
        $visit_registration_dto->visit_patient_type = $visit_registration_model->visit_patient_type;
        $visit_registration_dto->visit_patient_id   = $visit_registration_model->visit_patient_id;            
        $visit_patient_id = $visit_registration_model->visit_patient_id;
        if ($referral_dto->status == 'PROCESS' && $today != $referral_dto->visited_at) {        
            $current_visit_patient = $visit_registration_model->visitPatient;
            if (isset($visit_registration_dto->visit_patient)){
                $visit_patient_dto = &$visit_registration_dto->visit_patient;
                $visit_patient_dto->patient_type_service_id ??= $current_visit_patient->patient_type_service_id;
                $visit_patient_dto->payer_id ??= $current_visit_patient->payer_id;
                $visit_patient_dto->patient_id ??= $current_visit_patient->patient_id;
            }else{
                $visit_aptient_dto = $this->requestDTO(
                    app(config('app.contracts.VisitPatientData')),
                    [
                        'patient_type_service_id' => $current_visit_patient->patient_type_service_id,
                        'payer_id' => $current_visit_patient->payer_id,
                        'patient_id' => $current_visit_patient->patient_id,
                    ]
                );
                $visit_registration_dto->visit_patient = $visit_aptient_dto;
            }
        }
        $visit_registration_dto->visit_examination ??= $this->requestDTO(
            app(config('app.contracts.VisitExaminationData')),
            [
                'id' => null,
                'visit_patient_id' => $visit_patient_id ?? null,
                'visit_registration_id' => null,
            ]
        );
        $visit_registration_dto->visit_examination->visit_patient_id ??= $visit_patient_id ?? null;
    }
}
