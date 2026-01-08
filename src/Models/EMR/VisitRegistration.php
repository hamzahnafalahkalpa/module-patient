<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Concerns\Support\HasActivity;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModulePatient\Concerns\HasPractitionerEvaluation;
use Hanafalah\ModulePatient\Enums\VisitRegistration\Status;
use Hanafalah\ModulePatient\Resources\VisitRegistration\ShowVisitRegistration;
use Hanafalah\ModulePatient\Resources\VisitRegistration\ViewVisitRegistration;
use Hanafalah\ModulePatient\Enums\VisitRegistration\Activity;
use Hanafalah\ModulePatient\Enums\VisitRegistration\ActivityStatus;
use Hanafalah\ModulePayment\Concerns\HasPaymentSummary;
use Hanafalah\ModuleTransaction\Concerns\HasTransaction;

class VisitRegistration extends BaseModel
{
    use HasUlids, SoftDeletes, HasProps;
    use HasTransaction, HasPaymentSummary, HasActivity;
    use HasPractitionerEvaluation;

    //MEDIC SERVICE FLAG IN ENUM
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    protected $list       = [
        'id',
        'parent_id',
        'visit_registration_code',
        'visit_patient_id',
        'visit_patient_type',
        'medic_service_id',
        'service_cluster_id',
        'internal_referral_id',
        'referral_id',
        'warehouse_type',
        'warehouse_id',
        'status',
        'props'
    ];
    protected $show  = [];

    protected $casts = [
        'service_label_id'    => 'string',
        'patient_id'          => 'string',
        'patient_name'          => 'string',
        'patient_nik'          => 'string',
        'medic_service_name'  => 'string',
        'medic_service_label' => 'string',
        'visit_patient_reference_type' => 'string',
        'warehouse_type' => 'string',
        'warehouse_id' => 'string',
        'created_at'                   => 'date',
        'status'                       => 'string'
    ];

    public function getPropsQuery(): array
    {
        return [
            //FOR HEAD DOCTOR
            'service_label_id' => 'props->prop_service_label_ids',
            'service_cluster_id' => 'props->prop_service_cluster->id',
            'service_cluster_label' => 'props->prop_service_cluster->label',
            'medic_service_id' => 'props->prop_medic_service->id',
            'medic_service_label' => 'props->prop_medic_service->label',
            'visit_patient_reference_type' => 'props->prop_visit_patient->reference_type',
            'patient_id' => 'props->prop_visit_patient->patient->id',
            'patient_name' => 'props->prop_visit_patient->patient->name',
            'patient_nik' => 'props->prop_visit_patient->patient->people->nik'
        ];
    }

    protected static function booted(): void
    {
        parent::booted();
        static::creating(function ($query) {
            $query->visit_registration_code ??= static::hasEncoding('VISIT_REGISTRATION');
            $query->status ??= $query->getRegistrationStatus('DRAFT');
        });
        static::updated(function ($query) {
            if ($query->isDirty('status') && $query->status == Status::CANCELLED->value) {
                $payment_summary = $query->paymentSummary;
                if ($payment_summary->total_amount == $payment_summary->total_debt) {
                    $payment_summary->delete();
                }
            }
        });
    }

    public function getRegistrationStatus(string $status): string{
        return Status::from($status)->value;
    }

    public function viewUsingRelation(){
        return [
            'visitExamination',
            'visitPatient' => function ($query) {
                $query->with([
                    'reference',
                    'patient' => function ($query) {
                        $query->with([
                            'reference',
                            'cardIdentities'
                        ]);
                    }
                ]);
            }
        ];
    }

    public function showUsingRelation(){
        return [
            'itemRents',
            'practitionerEvaluation',
            'practitionerEvaluations',
            'warehouse',
            'examinationSummary',
            'visitPatient' => function ($query) {
                $query->with([
                    'reference',
                    'patient' => function ($query) {
                        $query->with([
                            'reference',
                            'cardIdentities'
                        ]);
                    }
                ]);
            }
        ];
    }

    public function getViewResource(){return ViewVisitRegistration::class;}
    public function getShowResource(){return ShowVisitRegistration::class;}
    public function warehouse(){return $this->morphTo();}
    public function visitPatient(){return $this->morphTo()->withoutGlobalScopes();}
    public function examinationSummary(){return $this->morphOneModel('ExaminationSummary', 'reference');}
    public function visitExamination(){return $this->hasOneModel('VisitExamination');}
    public function visitExaminations(){return $this->hasOneModel('VisitExamination');}
    public function assessments(){return $this->hasManyModel('Assessment');}
    public function medicService(){return $this->belongsToModel('MedicService');}
    public function itemRent(){return $this->morphOneModel('ItemRent','reference');}
    public function itemRents(){return $this->morphManyModel('ItemRent','reference');}
    public function modelHasService(){return $this->morphOneModel('ModelHasService', 'model');}
    public function modelHasServices(){return $this->morphManyModel('ModelHasService', 'model');}
    public function practitionerEvaluation(){return $this->morphOneModel('PractitionerEvaluation','reference');}
    public function treatments(){
        $treatment_types = config('module-treatment.treatment_types');
        $treatment_keys  = array_keys($treatment_types);
        return $this->hasManyModel('Assessment')->whereIn('morph',$treatment_keys);
    }
    public function services(){
        return $this->belongsToManyModel(
            'Service',
            'ModelHasService',
            'model_id',
            'service_id'
        )->where($this->ModelHasServiceModel()->getTable() . '.model_type', $this->getMorphClass());
    }

    public array $activityList = [
        Activity::POLI_EXAM->value . '_' . ActivityStatus::POLI_EXAM_QUEUE->value         => ['flag' => 'POLI_EXAM_QUEUE', 'message' => 'Pasien menunggu pemeriksaan'],
        Activity::POLI_EXAM->value . '_' . ActivityStatus::POLI_EXAM_START->value         => ['flag' => 'POLI_EXAM_START', 'message' => 'Pemeriksaan dimulai'],
        Activity::POLI_EXAM->value . '_' . ActivityStatus::POLI_EXAM_END->value           => ['flag' => 'POLI_EXAM_END', 'message' => 'Pemeriksaan selesai'],
        Activity::POLI_SESSION->value . '_' . ActivityStatus::POLI_SESSION_START->value   => ['flag' => 'POLI_SESSION_START', 'message' => 'Sesi pemeriksaan dibuka'],
        Activity::POLI_SESSION->value . '_' . ActivityStatus::POLI_SESSION_END->value     => ['flag' => 'POLI_SESSION_END', 'message' => 'Sesi pemeriksaan ditutup'],
        Activity::POLI_SESSION->value . '_' . ActivityStatus::POLI_SESSION_CANCEL->value  => ['flag' => 'POLI_SESSION_CANCEL', 'message' => 'Pemeriksaan Dibatalkan'],
    ];
}
