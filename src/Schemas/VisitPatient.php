<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModulePatient\Contracts\Data\VisitPatientData;
use Hanafalah\ModulePatient\Contracts\Schemas\VisitPatient as ContractsVisitPatient;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModulePatient\Enums\VisitPatient\{
    Activity,
    ActivityStatus,
    VisitStatus
};
use Hanafalah\ModulePatient\ModulePatient;
use Hanafalah\ModulePayment\Contracts\Data\InvoiceData;

class VisitPatient extends ModulePatient implements ContractsVisitPatient
{
    protected string $__entity = 'VisitPatient';
    public static $visit_patient_model;

    protected array $__cache = [
        'index' => [
            'name'     => 'visit_patient',
            'tags'     => ['visit_patient', 'visit_patient-index'],
            'duration' => 24 * 60
        ],
        'show' => [
            'name'     => 'visit_patient',
            'tags'     => ['visit_patient', 'visit_patient-show'],
            'duration' => 24 * 60
        ]
    ];

    public function prepareStoreVisitPatient(VisitPatientData $visit_patient_dto): Model{
        $visit_patient_model = $this->createVisitPatient($visit_patient_dto);
        $patient = &$visit_patient_model->patient;

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
        $visit_patient_dto->props->props['prop_transaction'] = $trx_transaction->toViewApi()->resolve();
        // $this->updatePaymentSummary($visit_patient_model, $attributes, $patient)
        //     ->createAgent($visit_patient_model, $attributes)
        //     ->createPatientType($visit_patient_model, $attributes)
        //     ->createConsumentTransaction($visit_patient_model, [
        //         'name'           => $patient->prop_people['name'],
        //         'phone'          => $phone ?? null,
        //         'reference_id'   => $patient->getKey(),
        //         'reference_type' => $patient->getMorphClass(),
        //         'patient'        => $patient
        //     ]);

        // $payment_summary_model = &$visit_patient_model->paymentSummary;
        // $payment_summary_model->transaction_id = $trx_transaction->getKey();
        // $payment_summary_model->save();

        //PROCESS VISIT REGISTRATIONS
        $visit_registrations = $visit_patient_dto?->visit_registrations;
        if (isset($visit_registrations) && count($visit_registrations) > 0){
            foreach ($visit_registrations as $visit_registration_dto) {
                $visit_registration_dto->visit_patient_id             = $visit_patient_model->getKey();
                $visit_registration_dto->visit_patient_type           = $visit_patient_model->getMorphClass();
                $visit_registration_dto->visit_patient_model          = $visit_patient_model;
                $visit_registration_dto->patient_type_service_id    ??= $visit_patient_model->patient_type_service_id;
                $visit_registration_dto->payment_summary->parent_id   = $visit_patient_model?->paymentSummary->getKey() ?? null;
                $visit_registration_dto->transaction->parent_id       = $trx_transaction?->getKey() ?? null;
                $this->schemaContract('visit_registration')->prepareStoreVisitRegistration($visit_registration_dto);
            }
        }
        $this->fillingProps($visit_patient_model, $visit_patient_dto->props);
        $visit_patient_model->save();
        return $visit_patient_model;
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
        $visit_patient_model->load(['transaction']);
        $visit_patient_model->setRelation('patient', $visit_patient_dto->patient_model ?? $visit_patient_model->patient);
        $this->initTransaction($visit_patient_dto, $visit_patient_model)
             ->initPaymentSummary($visit_patient_dto, $visit_patient_model);

        $this->fillingProps($visit_patient_model, $visit_patient_dto->props);
        $visit_patient_model->save();
        return static::$visit_patient_model = $visit_patient_model;
    }

    public function preparePushLifeCycleActivity(Model $visit_patient, Model $visit_patient_model, mixed $activity_status, int|array $statuses): self{
        $visit_patient->refresh();
        $prop_activity  = $visit_patient->prop_activity;

        $visit_patient_model->refresh();
        $visit_prop_activity  = $visit_patient_model->prop_activity;

        $statuses = $this->mustArray($statuses);
        $var_life_cycle = Activity::PATIENT_LIFE_CYCLE->value;
        $life_cycle = $prop_activity[$var_life_cycle] ?? [];

        foreach ($statuses as $key => $status) {
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

    protected function newVisitPatient(Model $visit_patient_model, array &$attributes): Model{
        $patient = $this->PatientModel()->find($attributes['patient_id']);
        if (!isset($patient)) throw new \Exception('Patient not found.', 422);

        $visit_patient_model = $visit_patient_model->create([
            'patient_id'     => $patient->getKey(),
            'parent_id'      => $attributes['parent_id'] ?? null,
            'reference_id'   => $attributes['reference_id'] ?? null,
            'reference_type' => $attributes['reference_type'] ?? null,
            'flag'           => $attributes['flag'] ?? null,
            'visited_at'     => now(),
            'status'         => VisitStatus::ACTIVE->value
        ]);

        if ($visit_patient_model->getMorphClass() == $this->VisitPatientModelMorph()) {
            $visit_patient_model->pushActivity(Activity::ADM_VISIT->value, [ActivityStatus::ADM_START->value]);
            $this->preparePushLifeCycleActivity($visit_patient_model, $visit_patient_model, 'ADM_VISIT', ['ADM_START']);

            if (isset($attributes['external_referral'])) {
                $externalRefferal = $this->schemaContract('external_referral');
                $externalRefferal = $externalRefferal->prepareStoreExternalReferral(
                    array_merge($attributes['external_referral'], ["visit_patient_id" => $visit_patient_model->getKey()])
                );
            }
        }

        $visit_patient_model->properties = $attributes['properties'] ?? [];
        $visit_patient_model->sync($patient, [
            'nik',
            'passport',
            'crew_id',
            'bpjs_code',
            'prop_people',
            'medical_record'
        ]);
        $visit_patient_model->setAttribute('prop_patient', $patient->getPropsKey());

        $reference = $patient->reference;
        if (\method_exists($reference, 'hasPhone')) {
            $phone = $reference->hasPhone?->phone ?? null;
        }

        $this->updatePaymentSummary($visit_patient_model, $attributes, $patient)
            ->createAgent($visit_patient_model, $attributes)
            ->createConsumentTransaction($visit_patient_model, [
                'name'           => $patient->prop_people['name'],
                'phone'          => $phone ?? null,
                'reference_id'   => $patient->getKey(),
                'reference_type' => $patient->getMorphClass(),
                'patient'        => $patient
            ]);
        $visit_patient_model->save();
        return $visit_patient_model;
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

    protected function updatePaymentSummary(Model &$model, array $attributes, ?Model $patient, ?string $message = null): self{
        $has_payer = isset($visit_patient_dto->payer_id);
        if (!$has_payer && isset($patient)) {
            $patient->modelHasOrganization()
                    ->where('organization_type', $this->PayerModelMorph())
                    ->delete();
        }

        if ($has_payer) {
            $this->createModelHasOrganization($model, $attributes);
        } else {
            if (isset($patient)) {
                $invoice = $this->schemaContract('invoice')->prepareStoreInvoice($this->requestDTO(InvoiceData::class,[
                    'consument_id'   => $patient->getKey(),
                    'consument_type' => $patient->getMorphClass(),
                    'consument_model' => $patient,
                    'payment_summary' => [
                        'name' => $message  
                    ],
                    'billing_at'     => null
                ]));

                $transaction                         = $model->transaction()->firstOrCreate();
                $trx_payment_summary                 = $transaction->paymentSummary;
                $trx_payment_summary->name           = "Total Tagihan untuk {$patient->prop_people['name']}";
                $trx_payment_summary->parent_id      = $paymentSummary->getKey();
                $trx_payment_summary->transaction_id = $transaction->getKey();
                $trx_payment_summary->save();

                $transaction->consument_name = $patient->prop_people['name'];
                $transaction->save();
            }
        }
        return $this;
    }

    protected function createModelHasOrganization(Model &$model, array $attributes){
        $model->modelHasOrganization()->updateOrCreate([
            'reference_id'       => $model->getKey(),
            'reference_type'     => $model->getMorphClass()
        ], [
            'organization_id'    => $attributes['payer_id'],
            'organization_type'  => $this->PayerModelMorph()
        ]);
        $payer   = $this->PayerModel()->findOrFail($attributes['payer_id']);
        $model->sync($payer, ['id', 'name']);
    }

    protected function createAgent(Model &$model, array $attributes): self{
        if (isset($attributes['agent_id'])) {
            $model->modelHasOrganization()->updateOrCreate([
                'reference_id'       => $model->getKey(),
                'reference_type'     => $model->getMorphClass(),
                'organization_type'  => $this->AgentModel()->getMorphClass()
            ], [
                'organization_id'    => $attributes['agent_id'],
            ]);
            $agent = $this->AgentModel()->findOrFail($attributes['agent_id']);
            $model->sync($agent, ['id', 'name']);
        }
        return $this;
    }

    protected function createInvoice($model){
        return $model->invoice()->firstOrCreate([
            'consument_id'   => $model->getKey(),
            'consument_type' => $model->getMorphClass(),
            'billing_at'     => null
        ]);
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
