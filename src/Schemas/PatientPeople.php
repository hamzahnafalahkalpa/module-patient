<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModulePatient\Contracts\Data\PatientData;
use Hanafalah\ModulePatient\Contracts\Schemas\PatientPeople as ContractsPatientPeople;
use Hanafalah\ModulePeople\Contracts\Data\FamilyRelationshipData;
use Hanafalah\ModulePeople\Contracts\Data\PeopleData;
use Illuminate\Database\Eloquent\Model;

class PatientPeople extends PackageManagement implements ContractsPatientPeople
{
    protected string $__entity = 'People';
    public static $people_model;

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

    // public function afterPatientCreated(Model $patient, Model $reference, PatientData $patient_dto){
    //     $this->createFamilyRelationShip($patient, $reference, $patient_dto);
    // }

    // protected function createFamilyRelationship(Model $reference,FamilyRelationshipData $family_relationshop_dto){
    //     $is_delete = true;
    //     if (isset($family_relationshop_dto)) {
    //         $attribute = $family_relationshop_dto;
    //         if (isset($attribute->role) || isset($attribute->phone)) {
    //             $reference->familyRelationship()->updateOrCreate([
    //                 'id' => isset($attribute->id) ? $attribute->id : null
    //             ], [
    //                 "people_id"  => $reference->getKey(),
    //                 'role'       => $attribute->role ?? null,
    //                 'name'       => $attribute->name ?? null,
    //                 'phone'      => $attribute->phone ?? null,
    //             ]);
    //             $is_delete = false;
    //         }
    //     }
    //     if ($is_delete) $reference->familyRelationship()->delete();
    // }
}
