<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModulePatient\Contracts\Data\PatientTypeData;
use Hanafalah\ModulePatient\Contracts\Schemas\PatientType as ContractsPatientType;
use Illuminate\Database\Eloquent\{
    Builder,
    Model
};

class PatientType extends PackageManagement implements ContractsPatientType
{
    protected string $__entity = 'PatientType';
    public static $patient_type_model;
    protected mixed $__order_by_created_at = false; //asc, desc, false

    protected array $__cache = [
        'index' => [
            'name'     => 'patient-type',
            'tags'     => ['patient-type', 'patient-type-index'],
            'forever'  => true
        ]
    ];

    public function prepareStorePatientType(PatientTypeData $patient_type_dto): Model
    {
        $add = [
            'name'  => $patient_type_dto->name,
            'flag'  => $patient_type_dto->flag,
            'label' => $patient_type_dto->label ?? 'Umum'
        ];
        if (isset($patient_type_dto->id)){
            $guard  = ['id' => $patient_type_dto->id];
            $create = [$add,$guard];
        }else{
            $create = [$add];
        }
        $patient_type = $this->usingEntity()->updateOrCreate(...$create);
        $this->fillingProps($patient_type, $patient_type_dto->props);
        $patient_type->save();
        return static::$patient_type_model = $patient_type;
    }

    public function patientType(mixed $conditionals = null): Builder{
        return $this->generalSchemaModel($conditionals)->when(isset(request()->flag),function($query){
            $query->flagIn(request()->flag);
        });
    }
}
