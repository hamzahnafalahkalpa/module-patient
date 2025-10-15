<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModulePatient\Contracts\Schemas\VisitPatient as ContractsVisitPatient;
use Hanafalah\ModulePatient\Contracts\Data\VisitPatientData;
use Hanafalah\ModulePatient\Contracts\Data\VisitRegistrationData;
use Hanafalah\ModulePatient\ModulePatient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Hanafalah\ModulePatient\Enums\VisitPatient\{
    Activity,
    ActivityStatus,
    VisitStatus
};

class VisitPatient extends ModulePatient implements ContractsVisitPatient
{
    protected string $__entity = 'VisitPatient';
    public $visit_patient_model;

    protected array $__cache = [
        'index' => [
            'name'     => 'visit_patient',
            'tags'     => [
                'visit_patient', 'visit_patient-index'
            ],
            'duration' => 24 * 60
        ]
    ];

    public function prepareStore(VisitPatientData $visit_patient_dto): Model{
        return $this->prepareStoreVisitPatient($visit_patient_dto);
    }


    public function prepareStoreVisitPatient(VisitPatientData $visit_patient_dto): Model{
        if (isset($visit_patient_dto->patient)) {
            $patient = $this->createPatient($visit_patient_dto);
            $visit_patient_dto->patient_id = $patient->getKey();
            $visit_patient_dto->patient_model = $patient;
        }
        $visit_patient_model = $this->createVisitPatient($visit_patient_dto);

        if ($visit_patient_model->getMorphClass() == $this->VisitPatientModelMorph()) {
            $visit_patient_model->pushActivity(Activity::ADM_VISIT->value, [ActivityStatus::ADM_START->value]);
            $this->preparePushLifeCycleActivity($visit_patient_model, $visit_patient_model, 'ADM_VISIT', ['ADM_START']);

            if (isset($visit_patient_dto->referral)) {
                $referral_dto = &$visit_patient_dto->referral;
                $referral_dto->visit_id     = $visit_patient_model->getKey();
                $referral_dto->visit_type   = $visit_patient_model->getMorphClass();
                $referral_dto->visit_model  = $visit_patient_model;
                $referral = $this->schemaContract('referral')->prepareStoreReferral($referral_dto);
                $visit_patient_dto->props->props['prop_referral'] = $referral->toViewApi()->resolve();
            }
        }
        $trx_transaction = &$visit_patient_model->transaction;

        //PROCESS VISIT REGISTRATIONS
        $visit_registrations = $visit_patient_dto?->visit_registrations;
        if (isset($visit_registrations) && count($visit_registrations) > 0){
            foreach ($visit_registrations as $visit_registration_dto) {
                $this->prepareStoreVisitRegistration($visit_registration_dto, $visit_patient_model, $trx_transaction);
            }
        }

        if (isset($visit_patient_dto->visit_registration)){
            $visit_registration_model = $this->prepareStoreVisitRegistration($visit_patient_dto->visit_registration, $visit_patient_model, $trx_transaction);
            $visit_patient_dto->props->props['prop_visit_registration'] = $visit_registration_model->toViewApiExcepts('visit_patient');
        }

        $this->fillingProps($visit_patient_model, $visit_patient_dto->props);
        $visit_patient_model->save();
        return $visit_patient_model;
    }

    protected function prepareStoreVisitRegistration(VisitRegistrationData &$visit_registration_dto, Model $visit_patient_model,?Model $trx_transaction = null): Model{
        $visit_registration_dto->visit_patient_id             = $visit_patient_model->getKey();
        $visit_registration_dto->visit_patient_type           = $visit_patient_model->getMorphClass();
        $visit_registration_dto->visit_patient_model          = $visit_patient_model;
        $visit_registration_dto->patient_type_service_id    ??= $visit_patient_model->patient_type_service_id;
        $visit_registration_dto->payment_summary->parent_id   = $visit_patient_model?->paymentSummary->getKey() ?? null;
        $visit_registration_dto->transaction->parent_id       = $trx_transaction?->getKey() ?? null;
        return $this->schemaContract('visit_registration')->prepareStoreVisitRegistration($visit_registration_dto);
    }

    protected function createPatient(VisitPatientData &$visit_patient_dto): Model{
        $patient = $this->schemaContract('patient')->prepareStorePatient($visit_patient_dto->patient);
        $visit_patient_dto->patient_id    = $patient->getKey();
        $visit_patient_dto->patient_model = $patient;
        $consument = &$visit_patient_dto->transaction->consument;
        $consument->name = $patient->name;
        $consument->reference_id = $visit_patient_dto->patient_id;
        return $patient;
    }

    protected function createVisitPatient(VisitPatientData $visit_patient_dto): Model{
        $add = [
            'parent_id'               => $visit_patient_dto->parent_id,
            'patient_id'              => $visit_patient_dto->patient_id,
            'reference_id'            => $visit_patient_dto->reference_id,
            'reference_type'          => $visit_patient_dto->reference_type,
            'flag'                    => $visit_patient_dto->flag,
            'reservation_id'          => $visit_patient_dto->reservation_id,
            'patient_type_service_id' => $visit_patient_dto->patient_type_service_id,
            'queue_number'            => $visit_patient_dto->queue_number
        ];
        if (isset($visit_patient_dto->id)){
            $guard  = ['id' => $visit_patient_dto->id];
            $create = [$guard,$add];
        }else{
            $add['id'] = null;
            $create = [$add];
        }
        $visit_patient_model = $this->usingEntity()->updateOrCreate(...$create);
        if (isset($visit_patient_dto->family_relationship) && isset($visit_patient_dto->family_relationship->name)) {
            $patient_model = $visit_patient_dto->patient_model;

            $family = $visit_patient_dto->family_relationship;
            $family->reference_type = $visit_patient_model->getMorphClass();
            $family->reference_id = $visit_patient_model->getKey();
            if (isset($patient_model) && $patient_model->reference_type == 'People'){
                $family->people_id = $patient_model->reference_id;
            }
            $family = $this->schemaContract('family_relationship')->prepareStoreFamilyRelationship($family);
            $visit_patient_dto->props->props['prop_family_relationship'] = $family->toViewApi()->resolve();
        } 
        
        $visit_patient_model->load(['transaction']);
        $visit_patient_dto->patient_model ??= $visit_patient_model->patient;
        if (isset($visit_patient_dto->patient_model)){
            $visit_patient_dto->props->props['prop_patient'] = $visit_patient_dto->patient_model->toViewApi()->resolve();
            $visit_patient_model->setRelation('patient', $visit_patient_dto->patient_model);
        }

        $this->initTransaction($visit_patient_dto, $visit_patient_model)
             ->initPaymentSummary($visit_patient_dto, $visit_patient_model);
        $visit_patient_dto->props->props['prop_transaction'] = $visit_patient_model->transaction->toViewApi()->resolve();
        $this->setPayer($visit_patient_model, $visit_patient_dto);

        $this->fillingProps($visit_patient_model, $visit_patient_dto->props);
        $visit_patient_model->save();
        return $this->visit_patient_model = $visit_patient_model;
    }

    public function preparePushLifeCycleActivity(Model $visit_patient, Model $visit_patient_model, mixed $activity_status, int|array $statuses): self{
        $visit_patient->refresh();
        $prop_activity  = $visit_patient->prop_activity;

        $visit_patient_model->refresh();
        $visit_prop_activity  = $visit_patient_model->prop_activity;

        $statuses = $this->mustArray($statuses);
        $var_life_cycle = Activity::PATIENT_LIFE_CYCLE->value;
        $life_cycle = $prop_activity[$var_life_cycle] ?? [];
        $activity_status = Str::lower($activity_status);
        foreach ($statuses as $key => $status) {
            $status = Str::lower($status);
            if (!is_numeric($key)) {
                $message = $status;
                $status = $key;
            } else {
                $message = $visit_prop_activity[$activity_status][$status]['message'] ?? null;
            }
            $activity_subject = &$visit_prop_activity[$activity_status];
            $activity_subject[$status] ??= [];
            $visit_model_prop = (array) $activity_subject[$status];
            $activity_by_status = $prop_activity[$activity_status][$status] ?? $visit_model_prop;
            if (isset($message)) {
                $activity_by_status['message'] = $message;
            }
            $existing_activity = collect($life_cycle)->first(function ($activity) use ($status, $message) {
                return isset($activity[$status]) && (isset($message) ? $activity[$status]['message'] == $message : true);
            });
            if (isset($existing_activity)) continue;
            $life_cycle[] = [$status => $activity_by_status];
        }
        $prop_activity[$var_life_cycle] = $life_cycle;
        $visit_patient->setAttribute('prop_activity', $prop_activity);
        $visit_patient->save();
        return $this;
    }

    protected function setPayer(Model &$visit_patient_model, VisitPatientData &$visit_patient_dto): self{
        if (config('module-patient.features.payer') == false) return $this;
        
        $payer = $this->PayerModel();
        if (isset($visit_patient_dto->payer)) {
            $payer = $this->schemaContract('Payer')->prepareStorePayer($visit_patient_dto->payer);

            $visit_patient_model->modelHasOrganization()->updateOrCreate([
                'organization_id'   => $payer->getKey(),
                'organization_type' => $payer->getMorphClass(),
            ]);
        } else {
            $visit_patient_model->modelHasOrganization()
                    ->where('organization_type', $this->PayerModel()->getMorphClass())
                    ->delete();
        }
        $props = &$visit_patient_dto->props->props;
        $props['payer_id']   = $payer?->getKey() ?? null;
        $props['prop_payer'] = $payer->toViewApiOnlies('id','name','flag','label');
        return $this;
    }

    protected function createConsumentTransaction(Model $visit_model, array $attributes): self{
        $transaction = $visit_model->transaction;
        if (isset($transaction)) {
            $add = [
                'name'   => $attributes['name'],
                'phone'  => $attributes['phone']
            ];
            if (isset($attributes['reference_id']) && isset($attributes['reference_type'])) {
                $guard = [
                    'reference_id'   => $attributes['reference_id'],
                    'reference_type' => $attributes['reference_type']
                ];
            } else {
                $guard = $add;
            }
            $consument = $this->ConsumentModel()->updateOrCreate($guard, $add);
            if (isset($attributes['patient'])) {
                $consument->setAttribute('prop_patient', $attributes['patient']->getPropsKey());
                $consument->save();
                $consument->refresh();
            }
            $props = [
                'id'    => $consument->getKey(),
                'name'  => $consument->name,
                'phone' => $consument->phone
            ];
            if (count($consument->getPropsKey() ?? []) > 0) {
                $props = $this->mergeArray($props, $consument->getPropsKey());
            }

            $transaction->consument_name = $consument->name;
            $transaction->setAttribute('prop_consument', $props);
            $transaction->save();

            $transaction->transactionHasConsument()->firstOrCreate([
                'consument_id' => $consument->getKey()
            ]);
        }
        return $this;
    }

    public function prepareDeleteVisitPatient(?array $attributes = null): mixed{
        $attributes ??= request()->all();
        if (!isset($attributes['id'])) throw new \Exception('Visit Patient not found.', 422);

        $visit_patient_model = $this->visitPatient()->with([
            'activity' => function ($query) {
                $query->where('activity_flag', Activity::ADM_VISIT->value);
            }
        ])->findOrFail($attributes['id']);
        if (!isset($visit_patient_model->activity)) throw new \Exception('Activity for this visit patient not found.', 422);

        if ($visit_patient_model->activity->activity_status == ActivityStatus::ADM_START->value) {
            $visit_patient_model->status                     = VisitStatus::CANCELLED->value;
            $visit_patient_model->pushActivity(Activity::ADM_VISIT->value, [ActivityStatus::ADM_CANCELLED->value]);
            $this->preparePushLifeCycleActivity($visit_patient_model, $visit_patient_model, 'ADM_VISIT', ['ADM_CANCELLED']);
            $visit_patient_model->save();
            $visit_patient_model->canceling();
        }
        throw new \Exception('Data cannot be cancelled anymore.', 422);
    }

}
