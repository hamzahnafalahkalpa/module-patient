<?php

namespace Hanafalah\ModulePatient;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModulePatient\Contracts;
use Hanafalah\ModulePatient\Supports\BaseModulePatient;

class ModulePatient extends BaseModulePatient implements Contracts\ModulePatient
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
            $transaction = $this->schemaContract(config('module-patient.transaction'))->prepareStoreTransaction($transaction_dto);
            $model->setRelation('transaction', $transaction);
            $transaction_dto->id = $transaction->getKey();
        }
        return $this;
    }

    protected function initPaymentSummary(mixed &$dto, Model &$model): self{
        if (config('module-patient.features.payment_summary') == false) return $this;

        if (isset($dto->payment_summary)){
            $payment_summary_dto = &$dto->payment_summary;
            $payment_summary_dto->reference_type  = $model->getMorphClass();
            $payment_summary_dto->reference_id    = $model->getKey();
            $payment_summary_dto->transaction_id  = $model->transaction->getKey();
            $payment_summary_dto->reference_model = $model;
            $payment_summary = $this->schemaContract('payment_summary')->prepareStorePaymentSummary($payment_summary_dto);
            $dto->payment_summary_model = $payment_summary;
            $model->setRelation('paymentSummary', $payment_summary);
            $payment_summary_dto->id = $payment_summary->getKey();
        }
        return $this;
    }

    protected function initPractitionerEvaluation(mixed &$practitioner_evaluation_dto, Model &$model): self{
        $practitioner_evaluation_dto->reference_type  = $model->getMorphClass();
        $practitioner_evaluation_dto->reference_id    = $model->getKey();
        $practitioner_evaluation         = $this->schemaContract('practitioner_evaluation')->prepareStorePractitionerEvaluation($practitioner_evaluation_dto);
        $practitioner_evaluation_dto->id = $practitioner_evaluation->getKey();
        return $this;
    }
}
