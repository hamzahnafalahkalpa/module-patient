<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModulePatient\Contracts\Data\PatientData;
use Hanafalah\ModulePatient\Contracts\Schemas\PatientPeople as ContractsPatientPeople;
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

    public function prepareStore(PatientData &$patient_dto): Model{
        $reference = $this->schemaContract('people')->prepareStorePeople($patient_dto->people);
        $patient_dto->reference_type = $reference->getMorphClass();
        $patient_dto->reference_id   = $reference->getKey();
        return $reference;
    }

    public function afterPatientCreated(Model $patient, Model $reference, PatientData $patient_dto){
        $this->createFamilyRelationShip($patient, $reference, $patient_dto);
    }

    protected function createFamilyRelationship(Model $patient, Model $reference,PatientData $patient_dto){
        $is_delete = true;
        if (isset($patient_dto->family_relationship)) {
            $attribute = $patient_dto->family_relationship;
            if (isset($attribute->role) || isset($attribute->phone)) {
                $reference->familyRelationship()->updateOrCreate([
                    'id' => isset($attribute->id) ? $attribute->id : null
                ], [
                    // "patient_id" => $patient->getKey(),
                    "people_id"  => $reference->getKey(),
                    'role'       => $attribute->role ?? null,
                    'name'       => $attribute->name ?? null,
                    'phone'      => $attribute->phone ?? null,
                ]);
                $is_delete = false;
            }
        }
        if ($is_delete) $reference->familyRelationship()->delete();
    }
}
