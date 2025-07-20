<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModulePatient\Contracts;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModulePatient\Contracts\Data\PatientTypeServiceData;
use Hanafalah\ModulePatient\Schemas\PatientType;

class PatientTypeService extends PatientType implements Contracts\Schemas\PatientTypeService
{
    protected string $__entity = 'PatientTypeService';
    public $patient_type_service_model;
    protected mixed $__order_by_created_at = false; //asc, desc, false

    public function prepareStorePatientTypeService(PatientTypeServiceData $patient_type_service_dto): Model{
        return $this->patient_type_service_model = parent::prepareStorePatientType($patient_type_service_dto);
    }
}
