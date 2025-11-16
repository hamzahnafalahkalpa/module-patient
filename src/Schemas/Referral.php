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
            $visit_registration_dto = &$referral_dto->visit_registration;
            $visit_registration_dto->referral_id = $referral->getKey();
            $visit_registration_dto->referral_model = $referral;
            if (!isset($visit_registration_dto->id)){
                switch (true){
                    case $referral_dto->visit_type == 'VisitRegistration':
                        $this->mapperForVisitRegistration($referral_dto);
                    break;
                }
            }
            $visit_registration = $this->schemaContract('visit_registration')->prepareStoreVisitRegistration($visit_registration_dto);
            $referral_dto->props->props['prop_visit_registration'] = $visit_registration->toViewApi()->resolve();
        }
        if (!isset($referral_dto->visit_model)) $referral_dto->visit_model = $this->{$referral_dto->visit_type.'Model'}()->findOrFail($referral_dto->visit_id);
        $referral_dto->props->props['prop_visit'] = $referral_dto->visit_model->toViewApi()->resolve();
        $this->fillingProps($referral,$referral_dto->props);
        $referral->save();
        return $this->referral_model = $referral;
    }

    protected function mapperForVisitRegistration(ReferralData &$referral_dto){
        $visit_registration_model = $this->VisitRegistrationModel()->findOrFail($referral_dto->visit_id);
        $visit_registration_dto = &$referral_dto->visit_registration;
        $visit_registration_dto->visit_patient_type = $visit_registration_model->visit_patient_type;
        $visit_registration_dto->visit_patient_id   = $visit_registration_model->visit_patient_id;
        $visit_registration_dto->visit_examination  ??= $this->requestDTO(app(config('app.contracts.VisitExaminationData')),[
            'id' => null,
            'visit_patient_id' => $visit_registration_dto->visit_patient_id,
            'visit_registration_id' => null
        ]);
    }
}
