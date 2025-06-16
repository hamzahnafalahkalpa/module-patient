<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModulePatient\Contracts\Data\PatientData;
use Hanafalah\ModulePatient\Contracts\Schemas\Patient as ContractsPatient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Hanafalah\ModulePatient\Enums\Patient\CardIdentity;

class Patient extends PackageManagement implements ContractsPatient
{
    protected string $__entity = 'Patient';
    public static $patient_model;

    protected array $__cache = [
        'index' => [
            'name'     => 'patient',
            'tags'     => ['patient', 'patient-index'],
            'forever'  => true
        ]
    ];

    public function prepareStorePatient(PatientData $patient_dto): Model{
        $patient = isset(request()->id) ? $this->patient()->with('reference')->find(request()->id) : $this->PatientModel();

        $reference_type = $this->getPatientReferenceType($patient);
        switch ($reference_type) {
            case 'ANIMAL':
            break;
            default:
                $reference = $this->createPeople($patient, $attributes);
            break;
        }

        $patient->refresh();

        if (isset($attributes['medical_record'])) {
            $patient->setCardIdentity(CardIdentity::MEDICAL_RECORD->value, $patient->medical_record);
        }

        if (request()->hasFile('profile')) {
            $name = $patient->uuid . '.jpg';
            $path = request()->file('profile')->storeAs('profile', $name, ['disk' => 's3']);
            $patient->profile = Storage::disk('s3')->url($path);
            $patient->save();
        }
        $patient->sync($reference);

        if (isset($attributes['OLD_MR'])) $patient->setCardIdentity(CardIdentity::OLD_MEDICAL_RECORD->value, $attributes['OLD_MR'] ?? "");
        $this->forgetTags('patient');

        return $patient;
    }

    protected function createFamilyRelationship(Model &$patient, Model $reference, $attributes){
        $is_delete = true;
        if (isset($attributes['family_relationship'])) {
            $attribute = $attributes['family_relationship'];
            if (isset($attribute['role']) || isset($attribute['phone'])) {
                $patient->familyRelationship()->updateOrCreate([
                    "patient_id" => $patient->getKey(),
                    "people_id"  => $reference->getKey()
                ], [
                    'role'  => $attribute['role'] ?? null,
                    'name'  => $attribute['name'] ?? null,
                    'phone' => $attribute['phone'] ?? null,
                ]);
                $is_delete = false;
            }
        }
        if ($is_delete) $patient->familyRelationship()->delete();
    }

    protected function getPatientReferenceType(Model $patient){
        $reference = $patient->reference ?? null;
        if (isset($patient->reference)) $reference_type = $reference->reference_type;
        return $reference_type ??= request()->reference_type;
    }

    protected function createPeople(Model &$patient, $attributes): Model{
        $reference = $patient->reference ?? null;
        $people    = $this->schemaContract('people')->prepareStorePeople($this->assocRequest(
            'reference_id',
            'nik',
            'passport',
            'residence_same_ktp',
            'addresses',
            'email',
            'father_name',
            'mother_name',
            'nationality',
            ...$this->diff($this->PeopleModel()->getFillable(), ['id', 'name', 'props']),
            ...[
                'phones' => $attributes['phones'] ?? [],
                'id'   => isset($reference) ? $reference->getKey() : null,
                'name' => trim(($attributes['first_name'] ?? '') . ' ' . ($attributes['last_name'] ?? '')),
            ],
        ));

        $patient->ihs_number     = $attributes['ihs_number'] ?? null;
        if (isset($attributes['medical_record'])) {
            $patient->medical_record = $attributes['medical_record'] ?? null;
        }
        $patient->father_name    = $attributes['father_name'] ?? null;
        $patient->mother_name    = $attributes['mother_name'] ?? null;
        $patient->bpjs_code      = $attributes['BPJS_CODE'] ?? null;
        $patient->nik            = $attributes['nik'] ?? null;
        $patient->passport       = $attributes['passport'] ?? null;
        $patient->nationality    = $attributes['nationality'] ?? null;
        $this->setPatientReference($patient, $people);
        $patient->save();

        $payer = $this->setPatientPayer($patient, $attributes);
        if (isset($attributes['BPJS_CODE'])) $patient->setCardIdentity(CardIdentity::BPJS_CODE->value, $attributes['BPJS_CODE'] ?? "");
        $this->createFamilyRelationShip($patient, $people, $attributes);
        return $people;
    }

    private function setPatientReference(Model &$patient, $reference): self{
        if (!$patient->id || !$patient->exists) {
            $patient->reference_id   = $reference->getKey();
            $patient->reference_type = $reference->getMorphClass();
        }
        return $this;
    }

    protected function setPatientPayer(Model &$patient, $attributes): self{
        if (isset($attributes['company_id'])) {
            $company = $this->CompanyModel()->findOrFail($attributes['company_id']);

            $patient->modelHasOrganization()->updateOrCreate([
                'organization_id'   => $company->getKey(),
                'organization_type' => $company->getMorphClass(),
            ]);

            $patient->sync($company, ['id', 'name']);
        } else {
            $patient->setAttribute('company', null);
            $patient->modelHasOrganization()
                    ->where('organization_type', $this->CompanyModel()->getMorphClass())
                    ->delete();
        }
        return $this;
    }
}
