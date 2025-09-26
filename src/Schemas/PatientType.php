<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModuleMedicService\Schemas\MedicService;
use Hanafalah\ModulePatient\Contracts\Data\PatientTypeData;
use Hanafalah\ModulePatient\Contracts\Schemas\PatientType as ContractsPatientType;
use Hanafalah\ModulePatient\Enums\PatientType\Flag;
use Illuminate\Database\Eloquent\{
    Builder,
    Model
};

class PatientType extends MedicService implements ContractsPatientType
{
    protected string $__entity = 'PatientType';
    public $patient_type_model;
    protected mixed $__order_by_created_at = false; //asc, desc, false

    protected array $__cache = [
        'index' => [
            'name'     => 'patient-type',
            'tags'     => ['patient-type', 'patient-type-index'],
            'duration' => 24 * 60
        ]
    ];

    public function prepareStorePatientType(PatientTypeData $patient_type_dto): Model{
        if ($patient_type_dto->flag == 'PatientType'){
            $patient_type_dto->label ??= 'Umum';
        }
        return $this->patient_type_model = $this->prepareStoreMedicService($patient_type_dto);
    }

    public function patientType(mixed $conditionals = null): Builder{
        return parent::medicService($conditionals);
    }
}
