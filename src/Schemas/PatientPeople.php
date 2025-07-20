<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModulePatient\Contracts\Data\PatientData;
use Hanafalah\ModulePatient\Contracts\Schemas\PatientPeople as ContractsPatientPeople;
use Hanafalah\ModulePeople\Contracts\Data\PeopleData;
use Illuminate\Database\Eloquent\Model;

class PatientPeople extends PackageManagement implements ContractsPatientPeople
{
    protected string $__entity = 'People';
    public $people_model;

    protected array $__cache = [
        'index' => [
            'name'     => 'patient_people',
            'tags'     => ['patient_people', 'patient_people-index'],
            'duration' => 24 * 60
        ]
    ];

    public function prepareStore(PeopleData &$people_dto): Model{
        $reference = $this->schemaContract('people')->prepareStorePeople($people_dto);        
        return $reference;
    }
}
