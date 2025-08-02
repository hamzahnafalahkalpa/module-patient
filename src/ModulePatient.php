<?php

namespace Hanafalah\ModulePatient;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModulePatient\Contracts;

class ModulePatient extends PackageManagement implements Contracts\ModulePatient
{
    protected $__screening_forms;
    protected $__open_forms;

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

    // public function getMedicServiceByServiceId(mixed $service_id): Model{
    //     return $this->ServiceModel()->with('reference')->findOrFail($service_id);
    // }

    // public function getMedicServiceById(mixed $service_id): Model{
    //     return $this->ServiceModel()->with('reference')
    //         ->where('reference_id', $service_id)
    //         ->where('reference_type', $this->MedicServiceModel()->getMorphClass())
    //         ->firstOrFail();
    // }

    // public function getMedicService(mixed $medic_service_id): Model{
    //     return $this->MedicServiceModel()->with('service')->findOrFail($medic_service_id);
    // }

    // public function getMedicServiceByFlag(?string $flag = null): Model{
    //     return $this->MedicServiceModel()->flagIn($flag ?? Label::OUTPATIENT->value)
    //         ->firstOrFail();
    // }
}
