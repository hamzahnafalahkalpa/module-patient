<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModulePatient\Contracts\Data\UnidentifiedPatientData;
use Hanafalah\ModulePatient\Contracts\Schemas\UnidentifiedPatient as ContractsUnidentifiedPatient;
use Illuminate\Database\Eloquent\Model;

class UnidentifiedPatient extends PackageManagement implements ContractsUnidentifiedPatient
{
    protected string $__entity = 'UnidentifiedPatient';
    public $unidentified_patient_model;

    protected array $__cache = [
        'index' => [
            'name'     => 'unidentified_patient',
            'tags'     => ['unidentified_patient', 'unidentified_patient-index'],
            'duration' => 24 * 60
        ]
    ];

    public function prepareStore(UnidentifiedPatientData &$unidentified_patient_dto): Model{
        $reference = $this->prepareStoreUnidentifiedPatient($unidentified_patient_dto);        
        return $reference;
    }

    public function prepareStoreUnidentifiedPatient(UnidentifiedPatientData $unidentified_patient_dto): Model{
        $add    = ['name' => $unidentified_patient_dto->name];
        $guard  = ['id' => $unidentified_patient_dto->id ?? null];

        $model = $this->usingEntity()->updateOrCreate($guard,$add);
        $this->fillingProps($model,$unidentified_patient_dto->props);
        $model->save();
        return $this->unidentified_patient_model = $model;
    }
}
