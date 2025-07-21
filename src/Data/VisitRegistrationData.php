<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModuleMedicService\Enums\Label;
use Hanafalah\ModulePatient\Contracts\Data\PractitionerEvaluationData;
use Hanafalah\ModulePatient\Contracts\Data\VisitExaminationData;
use Hanafalah\ModulePatient\Contracts\Data\VisitRegistrationData as DataVisitRegistrationData;
use Hanafalah\ModulePatient\Contracts\Data\VisitRegistrationPropsData;
use Hanafalah\ModulePayment\Contracts\Data\PaymentSummaryData;
use Hanafalah\ModuleTransaction\Contracts\Data\TransactionData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class VisitRegistrationData extends Data implements DataVisitRegistrationData{
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

    #[MapInputName('status')]
    #[MapName('status')]
    public mixed $status = null;

    #[MapInputName('payment_summary')]
    #[MapName('payment_summary')]
    public ?PaymentSummaryData $payment_summary;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?VisitRegistrationPropsData $props = null;

    public static function before(array &$attributes){
        $new = static::new();
        $medic_service = $new->MedicServiceModel()->findOrFail($attributes['medic_service_id']);
        $attributes['medic_service_model'] = $medic_service;
        $attributes['prop_medic_service'] = $medic_service->toViewApi()->resolve();

        $attributes['practitioner_evaluation'] ??= [];
        $practitioner_evaluation = &$attributes['practitioner_evaluation'];
        $practitioner_evaluation['practitioner_type'] ??= config('module-patient.practitioner');   
        $practitioner_model = app(config('database.models.'.$practitioner_evaluation['practitioner_type']));
        if (isset($practitioner_evaluation['practitioner_id'])){
            $practitioner_model = $practitioner_model->findOrFail($practitioner_evaluation['practitioner_id']);
        }
        $practitioner_evaluation['prop_practitioner'] = $practitioner_model->toViewApi()->resolve();
        if ($medic_service->label == Label::OUTPATIENT->value){
            $attributes['visit_examination'] ??= [
                "id" => null,
                'practitioner_evaluations' => []
            ];
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
        $medic_service = $new->MedicServiceModel()->findOrFail($data->medic_service_id);
        $data->medic_service_model = $medic_service;
        $props['prop_medic_service'] = $medic_service->toViewApi()->resolve();
        return $data;
    }
}