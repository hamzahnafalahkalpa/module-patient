<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModulePatient\{
    Contracts\Schemas\Referral as ContractsReferral,
    Enums\VisitRegistration\ActivityStatus,
    Enums\VisitRegistration\Activity,
    Enums\Referral\Status,
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
        $create = [
            'id'             => $referral_dto->id ?? null, 
            'visit_type'     => $referral_dto->visit_type, 
            'visit_id'       => $referral_dto->visit_id,
            'referral_type'    => $referral_dto->referral_type,
            'medic_service_id' => $referral_dto->medic_service_id
        ];

        $referral = $this->usingEntity()->firstOrCreate($create);

        if (isset($referral_dto->medic_service_id)){
            $medic_service = $this->MedicServiceModel()->findOrFail($referral_dto->medic_service_id);
            $referral_dto->props->props['prop_medic_service'] = $medic_service->toViewApi()->resolve();
        }

        if (!isset($referral_dto->visit_model)) $referral_dto->visit_model = $this->{$referral_dto->visit_type.'Model'}()->findOrFail($referral_dto->visit_id);

        $referral_dto->props->props['prop_visit'] = $referral_dto->visit_model->toViewApi()->resolve();

        $this->fillingProps($referral,$referral_dto->props);
        $referral->save();
        return $this->referral_model = $referral;
    }
}
