<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModulePatient\Contracts\Data\{
    CardIdentityData,
    PatientData,
    ProfilePatientData,
    ProfilePhotoData,
    VisitPatientData,
};
use Hanafalah\ModulePatient\Contracts\Schemas\Patient as ContractsPatient;
use Hanafalah\ModulePatient\Contracts\Schemas\ProfilePatient;
use Hanafalah\ModulePatient\Contracts\Schemas\ProfilePhoto;
use Hanafalah\ModulePatient\ModulePatient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Patient extends ModulePatient implements ContractsPatient, ProfilePatient, ProfilePhoto
{
    protected string $__entity = 'Patient';
    public $patient_model;
    public $is_recently_created = false;

    protected array $__cache = [
        'index' => [
            'name'     => 'patient',
            'tags'     => ['patient', 'patient-index'],
            'duration'  => 3*24*60
        ]
    ];

    protected function prepareStore(PatientData &$patient_dto){   
        if (isset($patient_dto->medical_record)){
            $patient_model = $this->usingEntity()->where('medical_record',$patient_dto->medical_record)->first();
            if (isset($patient_model) && !isset($patient_dto->id)) return $patient_model;
        }

        $reference_type   = $patient_dto->reference_type;
        $reference_schema = config('module-patient.patient_types.'.Str::snake($reference_type).'.schema');        
        if (isset($reference_schema) && isset($patient_dto->reference)) {
            $schema_reference          = $this->schemaContract(Str::studly($reference_schema));
            $reference                 = $schema_reference->prepareStore($patient_dto->reference);
            $patient_dto->reference_id = $reference->getKey();
            $patient_dto->props['prop_'.Str::snake($patient_dto->reference_type)] = $reference->toViewApi()->resolve();
            $patient_dto->name ??= $reference->name;
        }
        
        $add = [
            'name'           => $patient_dto->name,
            'patient_type_id' => $patient_dto->patient_type_id,
            'patient_occupation_id' => $patient_dto->patient_occupation_id,
            'row_imported' => $patient_dto->row_imported
        ];
        if (isset($patient_dto->medical_record)){
            $add['medical_record'] = $patient_dto->medical_record ?? null;
        }
        $guard = isset($patient_dto->id) 
            ? ['id' => $patient_dto->id]
            : [
                'reference_type' => $patient_dto->reference_type, 
                'reference_id' => $patient_dto->reference_id
            ];
        $patient = $this->usingEntity()->updateOrCreate($guard, $add);
        $this->schemaContract('patient_summary')->prepareStorePatientSummary(
            $this->requestDTO(config('app.contracts.PatientSummaryData'), [
                'patient_id' => $patient->getKey(),
                'patient_model' => $patient,
                'reference_type' => $patient->reference_type,
                'reference_id' => $patient->reference_id,
                'reference_model' => $patient->reference
            ])
        );
        $this->is_recently_created = $patient->wasRecentlyCreated;
        $patient->refresh();

        $profile_dto = $patient_dto->profile_dto;
        if (!isset($patient_dto->profile_dto)){
            $profile_dto = $this->requestDTO(ProfilePhotoData::class,[
                'id'      => $patient->getKey(),
                'profile' => $patient_dto->profile
            ]);
        }
        $patient = $this->prepareStoreProfilePhoto($profile_dto);

        $this->setPatientPayer($patient, $patient_dto);

        if (isset($patient_dto->card_identity)){            
            $this->patientIdentity(
                $patient, $patient_dto->card_identity,
                array_column(config('module-patient.patient_identities'),'value')
            );
        }
        if (isset($reference_schema) && method_exists($schema_reference, 'afterPatientCreated')) {
            $schema_reference->afterPatientCreated($patient, $reference, $patient_dto);
        }
        if (isset($reference)) $patient->sync($reference,$reference->toViewApi()->resolve());

        $this->afterPatientCreated($patient, $patient_dto);

        if (isset($patient_dto->visit_patient)){
            $visit_patient_dto = &$patient_dto->visit_patient;
            $visit_patient_dto['patient_id'] = $patient->getKey();
            $visit_patient_dto['patient_model'] = $patient;
            $visit_patient_dto['name'] = $patient->name;
            $patient_dto->visit_patient = $this->requestDTO(VisitPatientData::class,$visit_patient_dto);
            $visit_patient_dto = &$patient_dto->visit_patient;
            if (isset($visit_patient_dto->payer) || isset($visit_patient_dto->payer_id)){
                $visit_patient_dto->payer->id ??= $visit_patient_dto->payer_id;
                $patient_dto->payer_id ??= $visit_patient_dto->payer_id ?? null;
            }
            $visit_patient_dto->patient_id = $patient->getKey();
            $visit_patient_dto->patient_model = $patient;

            $visit_patient_model = $this->schemaContract('visit_patient')->prepareStoreVisitPatient($visit_patient_dto);
            $patient->setRelation('visit_patient', $visit_patient_model);
            $patient->load(['visitExamination'=>fn($q)=>$q->orderBy('created_at','desc')]);
        }
        return $patient;
    }

    protected function afterPatientCreated(Model &$patient, PatientData &$patient_dto): self{
        return $this;
    }

    public function prepareStorePatient(PatientData $patient_dto): Model{
        $patient = $this->prepareStore($patient_dto);        
        $this->fillingProps($patient,$patient_dto->props);
        $patient->save();
        return $patient;
    }

    protected function patientIdentity(Model &$patient, CardIdentityData $card_identity_dto, array $types){
        $card_identity = [];
        foreach ($types as $type) {
            $lower_type = Str::lower($type);
            $value = $card_identity_dto->{$lower_type} ?? null;
            if (isset($value)) $patient->setCardIdentity($lower_type, $card_identity_dto->{$lower_type});
            $card_identity[$lower_type] = $value;
        }
        $patient->setAttribute('prop_card_identity',$card_identity);
    }

    public function prepareStoreProfilePhoto(ProfilePhotoData $profile_photo_dto): Model{
        if (!isset($profile_photo_dto->id)) throw new \Exception('id not found');
        $patient          = $this->patient()->findOrFail($profile_photo_dto->id);
        $patient->profile = $patient->setProfilePhoto($profile_photo_dto->profile);
        $patient->save();
        return $this->patient_model = $patient;
    }

    public function storeProfilePhoto(?ProfilePhotoData $profile_photo_dto = null): array{
        return $this->transaction(function() use ($profile_photo_dto){
            return $this->showProfilePhoto($this->prepareStoreProfilePhoto($profile_photo_dto ?? $this->requestDTO(ProfilePhotoData::class)));
        });
    }

    public function prepareShowProfilePhoto(? Model $model = null, ?array $attributes = null): mixed{
        $attributes ??= \request()->all();
        $model ??= $this->getPatient();
        if (!isset($model)){
            $id   = $attributes['id'] ?? null;
            if (!isset($id)) throw new \Exception('id not found');
            $model = $this->patient()->with($this->showUsingRelation())->firstOrFail();
        }
        $this->patient_model = $model;
        if (isset($attributes['is_direct_photo']) && $attributes['is_direct_photo']) {
            return $model->getProfilePhoto();
        }else{
            return $model;
        }
    }

    public function showProfilePhoto(? Model $model = null): mixed{
        $is_direct_photo = (strpos(request()->header('accept'), 'image/*') === 0);
        if (!$is_direct_photo){
            return $this->transforming($this->usingEntity()->getViewFileResource(),function() use ($model){
                return $this->prepareShowProfilePhoto($model,request()->all());
            });
        }else{
            $attributes = \request()->all();
            $attributes['is_direct_photo'] = true;
            return $this->prepareShowProfilePhoto($model,$attributes);
        }
    }

    public function prepareStoreProfile(ProfilePatientData $profile_patient_dto): Model{
        if (!isset($profile_patient_dto->id) && !isset($profile_patient_dto->uuid)) throw new \Exception('id or uuid not found');

        $patient = $this->prepareStore($profile_patient_dto);
        return $this->patient_model = $patient;
    }

    public function storeProfile(? ProfilePatientData $profile_patient_dto = null): array{
        return $this->transaction(function() use ($profile_patient_dto){
            return $this->showPatient($this->prepareStoreProfile($profile_patient_dto ?? $this->requestDTO(ProfilePatientData::class)));
        });
    }

    public function prepareShowProfile(?Model $model = null, ?array $attributes = null): Model{
        $attributes ??= \request()->all();
        if (!isset($attributes['uuid'])) throw new \Exception('uuid not found');
        return $this->patient_model = $this->patient()->with($this->showUsingRelation())->whereHas('userReference',function($query) use ($attributes){
            $query->uuid($attributes['uuid']);
        })->firstOrFail();
    }

    public function showProfile(?Model $model = null): array{
        return $this->showEntityResource(function() use ($model){
            return $this->prepareShowProfile($model);
        });
    }

    protected function setPatientPayer(Model &$patient, PatientData &$patient_dto): self{
        if (config('module-patient.features.payer')){
            $payer = $this->PayerModel();
            if (isset($patient_dto->payer)) {
                $payer = $this->schemaContract('Payer')->prepareStorePayer($patient_dto->payer);
                $patient->modelHasOrganization()->updateOrCreate([
                    'organization_id'   => $payer->getKey(),
                    'organization_type' => $payer->getMorphClass(),
                ]);
            } else {
                $patient->modelHasOrganization()
                        ->where('organization_type', $this->PayerModel()->getMorphClass())
                        ->delete();
            }
            $props = &$patient_dto->props;
            $props['payer_id']   = $payer?->getKey() ?? null;
            $props['prop_payer'] = $payer->toViewApiOnlies('id','name','flag','label');
        }
        return $this;
    }
}
