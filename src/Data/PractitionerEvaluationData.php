<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\PractitionerEvaluationData as DataPractitionerEvaluationData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class PractitionerEvaluationData extends Data implements DataPractitionerEvaluationData{
    use HasRequestData;

    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('name')]
    #[MapName('name')]
    public ?string $name = null;

    #[MapInputName('practitioner_evaluation_model')]
    #[MapName('practitioner_evaluation_model')]
    public ?object $practitioner_evaluation_model = null;

    #[MapInputName('reference_type')]
    #[MapName('reference_type')]
    public mixed $reference_type = null;

    #[MapInputName('reference_id')]
    #[MapName('reference_id')]
    public mixed $reference_id = null;

    #[MapInputName('practitioner_type')]
    #[MapName('practitioner_type')]
    public ?string $practitioner_type = null;

    #[MapInputName('practitioner_id')]
    #[MapName('practitioner_id')]
    public ?string $practitioner_id = null;

    #[MapInputName('practitioner_model')]
    #[MapName('practitioner_model')]
    public ?object $practitioner_model = null;

    #[MapInputName('payment_details')]
    #[MapName('payment_details')]
    public array|null $payment_details = null;

    #[MapInputName('profession_id')]
    #[MapName('profession_id')]
    public mixed $profession_id = null;

    #[MapInputName('as_pic')]
    #[MapName('as_pic')]
    public ?bool $as_pic = false;

    #[MapInputName('is_commit')]
    #[MapName('is_commit')]
    public ?bool $is_commit = false;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;    

    public static function after(self $data): self{
        $new = static::new();
        $props = &$data->props;
        $data->is_commit ??= false;
        if (isset($data->payment_details) && is_array($data->payment_details) && count($data->payment_details) > 0 && config('module-patient.payment_detail') !== null){
            $payment_detail = config('module-patient.payment_detail');
            foreach ($data->payment_details as &$payment_detail_data) {
                $payment_detail_data = $new->requestDTO(config('app.contracts.'.$payment_detail.'Data'),$payment_detail_data);
            }
        }

        $data->as_pic ??= false;
        return $data;
    }
}