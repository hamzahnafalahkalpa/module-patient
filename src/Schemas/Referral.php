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
    public static $referral_model;

    protected array $__cache = [
        'index' => [
            'name'     => 'referral',
            'tags'     => ['referral', 'referral-index'],
            'duration' => 60
        ]
    ];

    public function prepareStoreReferral(ReferralData $referral_dto): Model{
        $reference = $this->schemaContract($referral_dto->reference_type)->prepareStore($referral_dto->reference);

        $create = [
            'id'             => $referral_dto->id ?? null, 
            'reference_type' => $referral_dto->reference_type, 
            'reference_id'   => $reference->getKey(), 
            'visit_type'     => $referral_dto->visit_type, 
            'visit_id'       => $referral_dto->visit_id,
        ];

        $referral = $this->usingEntity()->firstOrCreate($create);
        $referral_dto->props['prop_reference'] = $reference->toViewApi()->resolve();

        $this->fillingProps($referral,$referral_dto->props);
        $referral->save();
        return static::$referral_model = $referral;
    }
}
