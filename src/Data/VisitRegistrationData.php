<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModuleMedicService\Enums\Label;
use Hanafalah\ModulePatient\Concerns\Data\HasPractitionerEvaluation;
use Hanafalah\ModulePatient\Contracts\Data\PractitionerEvaluationData;
use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationData;
use Hanafalah\ModulePatient\Contracts\Data\VisitRegistrationData as DataVisitRegistrationData;
use Hanafalah\ModulePatient\Contracts\Data\VisitRegistrationPropsData;
use Hanafalah\ModulePayment\Contracts\Data\PaymentSummaryData;
use Hanafalah\ModuleTransaction\Contracts\Data\TransactionData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class VisitRegistrationData extends Data implements DataVisitRegistrationData{
    use HasPractitionerEvaluation;

    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('transaction')]
    #[MapName('transaction')]
    public ?TransactionData $transaction = null;

    #[MapInputName('visit_patient_id')]
    #[MapName('visit_patient_id')]
    public mixed $visit_patient_id = null;

    #[MapInputName('visit_patient_type')]
    #[MapName('visit_patient_type')]
    public ?string $visit_patient_type = null;

    #[MapInputName('visit_patient')]
    #[MapName('visit_patient')]
    public ?VisitPatientData $visit_patient = null;

    #[MapInputName('visit_patient_model')]
    #[MapName('visit_patient_model')]
    public ?object $visit_patient_model = null;

    #[MapInputName('medic_service_id')]
    #[MapName('medic_service_id')]
    public mixed $medic_service_id;

    #[MapInputName('medic_service_model')]
    #[MapName('medic_service_model')]
    public ?object $medic_service_model = null;

    #[MapInputName('service_cluster_id')]
    #[MapName('service_cluster_id')]
    public mixed $service_cluster_id = null;

    #[MapInputName('practitioner_evaluation')]
    #[MapName('practitioner_evaluation')]
    public ?PractitionerEvaluationData $practitioner_evaluation = null;

    #[MapInputName('visit_examination')]
    #[MapName('visit_examination')]
    public ?VisitExaminationData $visit_examination = null;

    #[MapInputName('referral_id')]
    #[MapName('referral_id')]
    public mixed $referral_id = null;

    #[MapInputName('referral_model')]
    #[MapName('referral_model')]
    public ?object $referral_model = null;

    #[MapInputName('status')]
    #[MapName('status')]
    public mixed $status = null;

    #[MapInputName('payment_summary')]
    #[MapName('payment_summary')]
    public ?PaymentSummaryData $payment_summary;

    #[MapInputName('examination')]
    #[MapName('examination')]
    public array|object|null $examination = null;

    #[MapInputName('item_rents')]
    #[MapName('item_rents')]
    #[DataCollectionOf(ItemRentData::class)]
    public ?array $item_rents = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?VisitRegistrationPropsData $props = null;

    public static function before(array &$attributes){
        $new = static::new();

        if (isset($attributes['examination']) && is_array($attributes['examination'])){
            $attributes['examination'] = $new->requestDTO(config('app.contracts.ExaminationData'),$attributes['examination']);
        }

        $medic_service = $new->MedicServiceModel()->with('parent')->findOrFail($attributes['medic_service_id']);
        $attributes['medic_service_model'] = $medic_service;
        $attributes['prop_medic_service'] = $medic_service->toViewApiOnlies('id','name','flag','label');
        $new->setupPractitionerEvaluation($attributes);
        if (
            $medic_service->label == Label::PHARMACY_UNIT->value || 
            $medic_service->label == Label::VERLOS_KAMER->value || 
            $medic_service->label == Label::EMERGENCY_UNIT->value || 
            isset($medic_service->parent) && $medic_service->parent->label == Label::OUTPATIENT->value
        ){
            $attributes['visit_examination'] ??= [
                "id" => null,
                'practitioner_evaluations' => []
            ];
            $attributes['visit_examination']['examination'] ??= $attributes['examination'] ?? null;
            unset($attributes['examination']);
        }

        $attributes['payment_summary'] = [
            "id" => null,
            'name' => 'Total Tagihan Kunjungan Poli '.$medic_service->name,
            "reference_type" => "VisitRegistration"
        ];

        $attributes['transaction'] = [
            'id' => null,
            "reference_type" => "VisitPatient"
        ];
    }

    public static function after(self $data): self{
        $new = static::new();
        $props = &$data->props->props;
        return $data;
    }
}