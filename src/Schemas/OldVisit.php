<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModulePatient\Contracts\Schemas\OldVisit as ContractsOldVisit;
use Hanafalah\ModulePatient\Contracts\Data\OldVisitData;
use Hanafalah\ModulePatient\ModulePatient;
use Illuminate\Database\Eloquent\Model;

class OldVisit extends ModulePatient implements ContractsOldVisit
{
    protected string $__entity = 'OldVisit';
    public $old_visit_model;
    public bool $is_recently_created = false;

    protected array $__cache = [
        'index' => [
            'name'     => 'old_visit',
            'tags'     => [
                'old_visit', 'old_visit-index'
            ],
            'duration' => 24 * 60
        ]
    ];

    public function prepareStoreOldVisit(OldVisitData $old_visit_dto): Model{
        $guard = [
            'patient_id' => $old_visit_dto->patient_id
        ];
        $old_visit_model = $this->usingEntity()->updateOrCreate($guard);
        $this->fillingProps($old_visit_model,$old_visit_dto->props);
        $old_visit_model->save();
        return $this->old_visit_model = $old_visit_model;
    }
}
