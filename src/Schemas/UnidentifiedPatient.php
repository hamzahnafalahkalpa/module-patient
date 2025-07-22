<?php

namespace Hanafalah\ModulePatient\Schemas;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModulePatient\{
    Supports\BaseModulePatient
};
use Hanafalah\ModulePatient\Contracts\Schemas\UnidentifiedPatient as ContractsUnidentifiedPatient;
use Hanafalah\ModulePatient\Contracts\Data\UnidentifiedPatientData;

class UnidentifiedPatient extends BaseModulePatient implements ContractsUnidentifiedPatient
{
    protected string $__entity = 'UnidentifiedPatient';
    public static $unidentified_patient_model;
    //protected mixed $__order_by_created_at = false; //asc, desc, false

    protected array $__cache = [
        'index' => [
            'name'     => 'unidentified_patient',
            'tags'     => ['unidentified_patient', 'unidentified_patient-index'],
            'duration' => 24 * 60
        ]
    ];

    public function prepareStoreUnidentifiedPatient(UnidentifiedPatientData $unidentified_patient_dto): Model{
        $add = [
            'name' => $unidentified_patient_dto->name
        ];
        $guard  = ['id' => $unidentified_patient_dto->id];
        $create = [$guard, $add];
        // if (isset($unidentified_patient_dto->id)){
        //     $guard  = ['id' => $unidentified_patient_dto->id];
        //     $create = [$guard, $add];
        // }else{
        //     $create = [$add];
        // }

        $unidentified_patient = $this->usingEntity()->updateOrCreate(...$create);
        $this->fillingProps($unidentified_patient,$unidentified_patient_dto->props);
        $unidentified_patient->save();
        return static::$unidentified_patient_model = $unidentified_patient;
    }
}