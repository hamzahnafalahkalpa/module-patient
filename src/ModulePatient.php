<?php

namespace Hanafalah\ModulePatient;

use Illuminate\Support\Str;
use Hanafalah\ModuleMedicService\Enums\Label;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModulePatient\Contracts;

class ModulePatient extends PackageManagement implements Contracts\ModulePatient
{

    protected static $__examination_summary;
    protected static $__practitioner_evaluation;
    protected static $__visit_registration;
    protected static $__visit_examination;
    protected static $__practitioner;
    protected static $__patient;
    protected static $__patient_summary;
    protected static $__pharmacy_sale;
    protected static $__screening_forms;
    protected static $__open_forms;

    public function __call($method, $arguments = [])
    {
        $result = parent::__call($method, $arguments);
        if (!isset($result)) {
            if (Str::startsWith($method, 'app') && Str::endsWith($method, 'Schema')) {
                $property = Str::replaceFirst('app', '', $method);
                $property = Str::replace('Schema', '', $property);
                $property = Str::snake($property);
                $var      = config('app.contracts.' . $property) ?? null;
                if (isset($var)) return app($var);
            }

            if (Str::startsWith($method, 'set')) {
                $property = Str::replaceFirst('set', '', $method);
                $property = Str::snake($property);
                $var      = static::${'__' . $property} = $arguments[0];
                return (isset($var)) ? $var : null;
            }
        }
        return $result;
    }

    protected function initTransaction(mixed &$dto, Model &$model): self{
        if (isset($dto->transaction)){
            $transaction_dto = &$dto->transaction;
            $transaction_dto->reference_type  = $model->getMorphClass();
            $transaction_dto->reference_id    = $model->getKey();
            $transaction_dto->reference_model = $model;
            $transaction = $this->schemaContract('transaction')->prepareStoreTransaction($transaction_dto);
            $model->setRelation('transaction', $transaction);
            $transaction_dto->id = $transaction->getKey();
        }
        return $this;
    }

    protected function initPaymentSummary(mixed &$dto, Model &$model): self{
        if (isset($dto->payment_summary)){
            $payment_summary_dto = &$dto->payment_summary;
            $payment_summary_dto->reference_type  = $model->getMorphClass();
            $payment_summary_dto->reference_id    = $model->getKey();
            $payment_summary_dto->transaction_id  = $model->transaction->getKey();
            $payment_summary_dto->reference_model = $model;
            $payment_summary = $this->schemaContract('payment_summary')->prepareStorePaymentSummary($payment_summary_dto);
            $model->setRelation('paymentSummary', $payment_summary);
            $payment_summary_dto->id = $payment_summary->getKey();
        }
        return $this;
    }

    protected function initPatientSummary($reference): self
    {
        if (isset($reference)) {
            switch ($reference->getMorphClass()) {
                case $this->PatientModel()->getMorphClass():
                    static::$__patient_summary = static::$__patient->patientSummary()->firstOrCreate([
                        'reference_id'   => static::$__patient->reference_id,
                        'reference_type' => static::$__patient->reference_type
                    ]);
                    break;
                case $this->VisitPatientModel()->getMorphClass():
                    static::$__visit_patient = $reference;
                    static::$__patient       = static::$__visit_patient->patient;
                    $this->initPatientSummary(static::$__patient);
                    break;
                case $this->PharmacySaleModel()->getMorphClass():
                    static::$__pharmacy_sale = $reference;
                    static::$__visit_patient = $reference;
                    if (isset($reference->patient_id)) {
                        static::$__patient = static::$__visit_patient->patient;
                        $this->initPatientSummary(static::$__patient);
                    }
                    break;
                case $this->VisitRegistrationModel()->getMorphClass():
                    $this->initPatientSummary($reference->visitPatient);
                    break;
                case $this->VisitExaminationModel()->getMorphClass():
                    $visit_registration = $reference->visitRegistration;
                    static::$__visit_registration = $visit_registration;
                    $this->initPatientSummary($visit_registration);
                    break;
            }
        }

        if (isset($patient_summary)) static::$__patient_summary = $patient_summary;
        return $this;
    }

    public function addTransactionIdTo(Model $model, mixed $transaction_id): Model
    {
        if ($transaction_id instanceof Model) $transaction_id = $transaction_id->getKey();

        $payment_summary = &$model->paymentSummary;
        if (!isset($payment_summary)) throw new \Exception('Payment summary not found', 422);

        $payment_summary->transaction_id = $transaction_id;
        $payment_summary->save();
        return $payment_summary;
    }

    public function getMedicServiceByServiceId(mixed $service_id): Model{
        return $this->ServiceModel()->with('reference')->findOrFail($service_id);
    }

    public function getMedicServiceById(mixed $service_id): Model
    {
        return $this->ServiceModel()->with('reference')
            ->where('reference_id', $service_id)
            ->where('reference_type', $this->MedicServiceModel()->getMorphClass())
            ->firstOrFail();
    }

    public function getMedicService(mixed $medic_service_id): Model{
        return $this->MedicServiceModel()->with('service')->findOrFail($medic_service_id);
    }

    public function getMedicServiceByFlag(?string $flag = null): Model{
        return $this->MedicServiceModel()->flagIn($flag ?? Label::OUTPATIENT->value)
            ->firstOrFail();
    }
}
