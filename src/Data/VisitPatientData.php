<?php

namespace Hanafalah\ModulePatient\Data;

use Carbon\Carbon;
use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\PatientData;
use Hanafalah\ModulePatient\Contracts\Data\ReferralData;
use Hanafalah\ModulePatient\Contracts\Data\VisitPatientData as DataVisitPatientData;
use Hanafalah\ModulePayment\Contracts\Data\PaymentSummaryData;
use Hanafalah\ModuleTransaction\Contracts\Data\TransactionData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\DateFormat;

class VisitPatientData extends Data implements DataVisitPatientData{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('parent_id')]
    #[MapName('parent_id')]
    public mixed $parent_id = null;

    #[MapInputName('patient_id')]
    #[MapName('patient_id')]
    public mixed $patient_id = null;

    #[MapInputName('patient')]
    #[MapName('patient')]
    public ?PatientData $patient = null;

    #[MapInputName('patient_model')]
    #[MapName('patient_model')]
    public ?object $patient_model = null;

    #[MapInputName('transaction')]
    #[MapName('transaction')]
    public ?TransactionData $transaction = null;

    #[MapInputName('reference_type')]
    #[MapName('reference_type')]
    public ?string $reference_type = null;

    #[MapInputName('reference_id')]
    #[MapName('reference_id')]
    public mixed $reference_id = null;

    #[MapInputName('reference_model')]
    #[MapName('reference_model')]
    public ?object $reference_model = null;

    #[MapInputName('reservation_id')]
    #[MapName('reservation_id')]
    public mixed $reservation_id = null;


    #[MapInputName('patient_type_service_id')]
    #[MapName('patient_type_service_id')]
    public mixed $patient_type_service_id;

    #[MapInputName('queue_number')]
    #[MapName('queue_number')]
    public mixed $queue_number = null;

    #[MapInputName('visited_at')]
    #[MapName('visited_at')]
    #[DateFormat('Y-m-d')]
    public ?string $visited_at = null;

    #[MapInputName('reported_at')]
    #[MapName('reported_at')]
    public ?Carbon $reported_at = null;

    #[MapInputName('flag')]
    #[MapName('flag')]
    public string $flag;

    #[MapInputName('referral')]
    #[MapName('referral')]
    public ?ReferralData $referral = null;

    #[MapInputName('visit_registration')]
    #[MapName('visit_registration')]
    public ?VisitRegistrationData $visit_registration = null;

    #[MapInputName('visit_registrations')]
    #[MapName('visit_registrations')]
    #[DataCollectionOf(VisitRegistrationData::class)]
    public ?array $visit_registrations;

    #[MapInputName('payment_summary')]
    #[MapName('payment_summary')]
    public ?PaymentSummaryData $payment_summary;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?VisitPatientPropsData $props = null;

    public static function before(array &$attributes){
        $new = static::new();
        $attributes['flag'] ??= 'VisitPatient';

        if (isset($attributes['patient_id'])){
            $attributes['patient_model'] = $patient = $new->PatientModel()->with('reference')->findOrFail($attributes['patient_id']);
            $attributes['prop_patient'] = $patient->toViewApi()->resolve();
        }

        $attributes['transaction'] = [
            'id' => null,
            "reference_type" => "VisitPatient",
            'consument' => [
                'id'             => null,
                'phone'          => $phone ?? null,
                'name'           => $patient?->name ?? null,
                'reference_type' => 'Patient',
                'reference_id'   => $attributes['patient_id'] ?? null
            ]
        ];
        $attributes['payment_summary'] = [
            "id" => null,
            'name'           =>  trim('Total Tagihan Pasien '.($patient?->name ?? '')),
            "reference_type" => "VisitPatient"
        ];
    }

    public static function after(self $data): self{
        $new = static::new();
        $props = &$data->props->props;
        
        $patient_type_service = $new->PatientTypeServiceModel();
        $patient_type_service = (isset($data->patient_type_service_id)) ? $patient_type_service->findOrFail($data->patient_type_service_id) : $patient_type_service;
        $props['prop_patient_type_service'] = $patient_type_service->toViewApi()->resolve();
        return $data;
    }
}