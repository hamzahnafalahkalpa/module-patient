<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModulePatient\Contracts\Data\PatientOccupationData;
use Hanafalah\ModulePatient\Contracts\Schemas\PatientOccupation as ContractsPatientOccupation;
use Hanafalah\ModuleProfession\Schemas\Occupation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PatientOccupation extends Occupation implements ContractsPatientOccupation
{
    protected string $__entity = 'PatientOccupation';
    public static $patient_occupation_model;

    protected array $__cache = [
        'index' => [
            'name'     => 'patient_occupation',
            'tags'     => ['patient_occupation', 'patient_occupation-index'],
            'duration' => 24 * 60
        ]
    ];

    public function prepareStorePatientOccupation(PatientOccupationData $patient_occupation_dto): Model
    {
        $model = parent::prepareStoreProfession($patient_occupation_dto);
        return static::$patient_occupation_model = $model;
    }

    public function patientOccupation(mixed $conditionals = null): Builder{
        return $this->generalSchemaModel($conditionals)->whereNull('parent_id');
    }
}
