<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\ProfilePatientData;

interface ProfilePatient extends DataManagement
{
    public function prepareStoreProfile(ProfilePatientData $profile_patient_dto): Model;
    public function storeProfile(? ProfilePatientData $profile_patient_dto = null): array;
    public function showProfile(? Model $model = null): array;
}
