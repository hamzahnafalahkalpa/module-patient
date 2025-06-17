<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModulePatient\Contracts\Data\PatientData;
use Hanafalah\ModulePatient\Contracts\Schemas\PatientPeople as ContractsPatientPeople;

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

    public function prepareStore(PatientData &$patient_dto){
        $reference = $this->schemaContract('people')->prepareStorePeople($patient_dto->people);
        $patient_dto->reference_type = $reference->getMorphClass();
        $patient_dto->reference_id   = $reference->getKey();
        // $this->createFamilyRelationShip($patient, $people, $attributes);
    }

    // $reference = $patient->reference ?? null;
    // $people    = $this->schemaContract('people')->prepareStorePeople($this->assocRequest(
    //     'reference_id',
    //     'nik',
    //     'passport',
    //     'residence_same_ktp',
    //     'addresses',
    //     'email',
    //     'father_name',
    //     'mother_name',
    //     'nationality',
    //     ...$this->diff($this->PeopleModel()->getFillable(), ['id', 'name', 'props']),
    //     ...[
    //         'phones' => $attributes['phones'] ?? [],
    //         'id'   => isset($reference) ? $reference->getKey() : null,
    //         'name' => trim(($attributes['first_name'] ?? '') . ' ' . ($attributes['last_name'] ?? '')),
    //     ],
    // ));

    // $patient->father_name    = $attributes['father_name'] ?? null;
    // $patient->mother_name    = $attributes['mother_name'] ?? null;
    // $patient->bpjs_code      = $attributes['BPJS_CODE'] ?? null;
    // $patient->nik            = $attributes['nik'] ?? null;
    // $patient->passport       = $attributes['passport'] ?? null;
    // $patient->nationality    = $attributes['nationality'] ?? null;
    // $this->setPatientReference($patient, $people);
    // $patient->save();

    // $payer = $this->setPatientPayer($patient, $attributes);
    // if (isset($attributes['BPJS_CODE'])) $patient->setCardIdentity(CardIdentity::BPJS_CODE->value, $attributes['BPJS_CODE'] ?? "");
    // $this->createFamilyRelationShip($patient, $people, $attributes);
    // return $people;
}
