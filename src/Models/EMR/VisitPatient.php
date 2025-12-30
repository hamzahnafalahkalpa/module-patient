<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\{
    Concerns\Support\HasActivity,
    Models\BaseModel
};

use Hanafalah\ModulePatient\Enums\{
    VisitRegistration\Activity as VisitRegistrationActivity,
    VisitRegistration\ActivityStatus as VisitRegistrationActivityStatus
};

use Hanafalah\ModulePatient\{
    Enums\VisitPatient\VisitStatus,
    Resources\VisitPatient\ShowVisitPatient,
    Resources\VisitPatient\ViewVisitPatient
};
use Hanafalah\ModulePatient\Concerns\Emr\HasExaminationSummary;
use Hanafalah\ModulePatient\Concerns\HasPractitionerEvaluation;
use Hanafalah\ModulePatient\Enums\VisitPatient\{
    Activity,
    ActivityStatus
};
use Hanafalah\ModulePatient\Enums\VisitRegistration\Status;
use Hanafalah\ModulePayment\Concerns\HasPaymentSummary;
use Hanafalah\ModuleTransaction\Concerns\HasTransaction;

class VisitPatient extends BaseModel
{
    use HasUlids, SoftDeletes, HasProps, HasActivity, 
    HasPaymentSummary, HasPractitionerEvaluation,
    HasTransaction, HasExaminationSummary;

    const CLINICAL_VISIT = 'VisitPatient';
    const STATUS_ACTIVE  = 'ACTIVE';

    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    protected $list       = [
        'id',
        'parent_id',
        'visit_code',
        'patient_id',
        'reference_id',
        'reference_type',
        'flag',
        'reservation_id',
        'queue_number',
        'visited_at',
        'reported_at',
        'patient_type_service_id',
        'status',
        'props'
    ];
    protected $show = [];

    protected $casts = [
        'patient_id'     => 'string',
        'name'           => 'string',
        'queue_number'   => 'string',
        'created_at'     => 'datetime',
        'nik'            => 'string',
        'dob'            => 'immutable_date',
        'medical_record' => 'string',
        'visited_at'     => 'datetime',
        'reported_at'    => 'datetime',
        'consument_name'  => 'string',
        'consument_phone' => 'string',
        'medic_service_label' => 'string'
    ];

    public function getPropsQuery(): array
    {
        return [
            'name'           => 'props->prop_patient->prop_people->name',
            'dob'            => 'props->prop_patient->prop_people->dob',
            'nik'            => 'props->prop_patient->people->card_identity->nik',
            'medical_record' => 'props->prop_patient->medical_record',
            'medic_service_label' => 'props->prop_visit_registration->prop_medic_service->label'
        ];
    }

    protected static function booted(): void{
        parent::booted();
        static::addGlobalScope('flag', function ($query) {
            $query->flagIn((new static)->getMorphClass());
        });
        static::creating(function ($query) {
            $query->visit_code ??= static::hasEncoding('VISIT_PATIENT');
            $query->flag       ??= $query->getMorphClass();
            $query->status     ??= self::getVisitStatus('ACTIVE');
            if (!isset($query->reservation_id) && $query->visited_at === null) {
                $query->visited_at = now();
            }
        });
        static::updated(function ($query) {
            //WHEN DELETING
            if ($query->isDirty('status') && $query->status ==self::getVisitStatus('CANCELLED')) {
                $payment_summary = $query->paymentSummary;
                if (isset($payment_summary)) {
                    if ($payment_summary->total_amount == $payment_summary->total_debt) {
                        $payment_summary->delete();
                    }
                }

                $query->load([
                    'visitRegistrations' => function ($query) {
                        $query->whereNot('status', static::getRegistrationStatus('CANCELLED'));
                    }
                ]);
                $visit_registrations = $query->visitRegistrations;
                foreach ($visit_registrations as $visit_registration) {
                    $visit_registration->status = static::getRegistrationStatus('CANCELLED');
                    $visit_registration->pushActivity(
                        VisitRegistrationActivity::POLI_SESSION->value, [
                            VisitRegistrationActivityStatus::POLI_SESSION_CANCEL->value
                        ]
                    );
                    $visit_registration->save();
                }
            }
        });
    }

    public static function getVisitStatus(string $status){
        return VisitStatus::from($status)->value;
    }

    public static function getRegistrationStatus(string $status){
        return Status::from($status)->value;
    }

    public function viewUsingRelation(): array{
        return ['practitionerEvaluation','visitRegistration.visitExamination'];
    }

    public function showUsingRelation(): array{
        return [
            'reference',
            'payer',
            'patientTypeService',
            'patient.reference', 
            'practitionerEvaluation',
            // 'reservation',
            'visitRegistrations' => function ($query) {
                $query->with(['warehouse','practitionerEvaluations','visitExamination']);
            },
            // 'organizations',
            // 'services',
        ];
    }
    
    public function getViewResource(){return ViewVisitPatient::class;}
    public function getShowResource(){return ShowVisitPatient::class;}    

    public function patient(){return $this->belongsToModel('Patient');}
    public function reservation(){return $this->belongsToModel('Reservation');}
    public function transaction(){return $this->morphOneModel('Transaction', 'reference');}
    public function visitRegistration(){return $this->morphOneModel('VisitRegistration', 'visit_patient');}
    public function visitExamination(){return $this->hasOneModel('VisitExamination', 'visit_patient_id');}
    public function visitRegistrations(){return $this->morphManyModel('VisitRegistration', 'visit_patient');}
    public function patientDischarge(){return $this->hasOneModel('PatientDischarge', 'visit_patient_id');}
    public function modelHasOrganization(){return $this->morphOneModel('ModelHasOrganization', 'model');}
    public function modelHasOrganizations(){return $this->morphManyModel('ModelHasOrganization', 'model');}    
    public function familyRelationship(){return $this->morphOneModel('FamilyRelationship', 'reference');}    
    public function reference(){return $this->morphTo();}
    public function modelHasService(){return $this->morphOneModel('ModelHasService', 'model');}
    public function modelHasServices(){return $this->morphManyModel('ModelHasService', 'model');}
    public function patientSummary(){return $this->hasOneModel('PatientSummary','visit_patient_id');}
    public function patientTypeService(){return $this->belongsToModel('PatientTypeService');}
    public function practitionerEvaluation(){return $this->morphOneModel('PractitionerEvaluation','reference')->where('props->role_as','ADMITTER');}
    // public function patientTypeHistory(){return $this->hasOneModel('PatientTypeHistory', 'visit_patient_id');}
    // public function patientTypeHistories(){return $this->hasManyModel('PatientTypeHistory', 'visit_patient_id');}
    public function cardStocks(){
        $transaction_model = $this->TransactionModel();
        return $this->hasManyThroughModel(
            'CardStock',
            'Transaction',
            $transaction_model->getTableName() . '.reference_id',
            $transaction_model->getForeignKey(),
            $this->getKeyName(),
            $transaction_model->getKeyName()
        )->where($transaction_model->getTableName() . '.reference_type', $this->getMorphClass());
    }
    public function services(){
        return $this->belongsToManyModel(
            'Service',
            'ModelHasService',
            'model_has_services.model_id',
            'service_id'
        )->where('model_has_services.model_type', $this->getMorphClass());
    }
    
    public function payer(){
        $payer_table          = $this->PayerModel()->getTableName();
        $model_has_table_name = $this->ModelHasOrganizationModel()->getTableName();
        return $this->hasOneThroughModel(
            'Payer',
            'ModelHasOrganization',
            'model_id',
            'id',
            'id',
            'organization_id'
        )->where($model_has_table_name . '.model_type', $this->getMorphClass())
            ->where($model_has_table_name . '.organization_type', $this->PayerModelMorph())
            ->select([$payer_table . '.*', $model_has_table_name . '.*', $payer_table . '.id as id']);
    }
    
    public function agent(){
        $agent_table = $this->AgentModel()->getTableName();
        $model_has_table_name = $this->ModelHasOrganizationModel()->getTableName();
        return $this->hasOneThroughModel(
            'Agent',
            'ModelHasOrganization',
            'model_id',
            'id',
            'id',
            'organization_id'
        )->where('model_type', $this->getMorphClass())
            ->where('organization_type', $this->AgentModel()->getMorphClass())
            ->select([$agent_table . '.*', $model_has_table_name . '.*', $agent_table . '.id as id']);
    }

    public function organization(){
        $organization_table = $this->OrganizationModel()->getTableName();
        $model_has_table_name = $this->ModelHasOrganizationModel()->getTableName();
        return $this->hasOneThroughModel(
            'Organization',
            'ModelHasOrganization',
            'model_id',
            'id',
            'id',
            'organization_id'
        )->where('model_type', $this->getMorphClass())
            ->select([$organization_table . '.*', $model_has_table_name . '.*', $organization_table . '.id as id']);
    }

    public function organizations(){
        $organization_table = $this->OrganizationModel()->getTableName();
        $model_has_table_name = $this->ModelHasOrganizationModel()->getTableName();
        return $this->hasManyThroughModel(
            'Organization',
            'ModelHasOrganization',
            'model_id',
            'id',
            'id',
            'organization_id'
        )->where('model_type', $this->getMorphClass())
            ->select([$organization_table . '.*', $model_has_table_name . '.*', $organization_table . '.id as id']);
    }

    public array $activityList = [
        Activity::ADM_VISIT->value . '_' . ActivityStatus::ADM_START->value     => ['flag' => 'adm_start', 'message' => 'Administrasi dibuat'],
        Activity::ADM_VISIT->value . '_' . ActivityStatus::ADM_PROCESSED->value => ['flag' => 'adm_processed', 'message' => 'Pasien dalam antrian layanan'],
        Activity::ADM_VISIT->value . '_' . ActivityStatus::ADM_FINISHED->value  => ['flag' => 'adm_finished', 'message' => 'Pasien selesai layanan'],
        Activity::ADM_VISIT->value . '_' . ActivityStatus::ADM_CANCELLED->value => ['flag' => 'adm_cancelled', 'message' => 'Transaksi dibatalkan'],
    ];
}
