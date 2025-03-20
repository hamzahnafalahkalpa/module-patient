<?php

namespace Zahzah\ModulePatient\Models\EMR;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Zahzah\LaravelHasProps\Concerns\HasProps;
use Zahzah\LaravelSupport\Concerns\Support\HasActivity;
use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\ModulePatient\Enums\VisitRegistration\RegistrationStatus;
use Zahzah\ModulePatient\Resources\VisitRegistration\ShowVisitRegistration;
use Zahzah\ModulePatient\Resources\VisitRegistration\ViewVisitRegistration;
use Zahzah\ModuleTransaction\Concerns\HasPaymentSummary;
use Zahzah\ModulePatient\Enums\VisitRegistration\Activity;
use Zahzah\ModulePatient\Enums\VisitRegistration\ActivityStatus;
use Zahzah\ModuleTransaction\Concerns\HasTransaction;

class VisitRegistration extends BaseModel{
    use HasUlids, SoftDeletes, HasProps;
    use HasTransaction, HasPaymentSummary, HasActivity;

    //MEDIC SERVICE FLAG IN ENUM
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    protected $list       = [
        'id','visit_registration_code','visit_patient_id','visit_patient_type',
        'medic_service_id','patient_type_id','referral_id',
        'status','props'
    ];
    protected $show  = ['parent_id','head_doctor_id','head_doctor_type'];

    protected $casts = [
        'name'                => 'string',
        'service_label_id'    => 'string',
        'created_at'          => 'date'
    ];

    public function getPropsQuery(): array{
        return [
            //FOR HEAD DOCTOR
            'name' => 'props->prop_people->name',
            'service_label_id' => 'props->prop_service_label_ids'
        ];
    }

    protected static function booted(): void{
        parent::booted();
        static::creating(function($query){
            if (!isset($query->visit_registration_code)) {
                $query->visit_registration_code = static::hasEncoding('VISIT_REGISTRATION');
            }
            if (!isset($query->status)) $query->status = RegistrationStatus::DRAFT->value;
        });
        static::created(function($query){
            $visit_patient = $query->visitPatient;
            $visit_patient->patientTypeHistory()->firstOrCreate([
                'patient_type_id' => $query->patient_type_id
            ]);
        });
        static::updated(function($query){
            if ($query->isDirty('status') && $query->status == RegistrationStatus::CANCELLED->value){
                $payment_summary = $query->paymentSummary;
                if ($payment_summary->total_amount == $payment_summary->total_debt){
                    $payment_summary->delete();
                }
            }
        });
    }

    public function toShowApi(){
        return new ShowVisitRegistration($this);
    }

    public function toViewApi(){
        return new ViewVisitRegistration($this);
    }

    public function getStatusSpell(){
        switch ($this->status) {
            case RegistrationStatus::DRAFT->value      : return 'DRAFT';break;
            case RegistrationStatus::PROCESSING->value : return 'PROCESSING';break;
            case RegistrationStatus::CANCELLED->value  : return 'CANCELLED';break;
            case RegistrationStatus::COMPLETED->value  : return 'COMPLETED';break;
        }
    }

    public function visitPatient(){return $this->morphTo();}
    public function visitExamination(){return $this->hasOneModel('VisitExamination');}
    public function visitExaminations(){return $this->hasOneModel('VisitExamination');}
    public function medicService(){return $this->belongsToModel('MedicService');}
    public function patientType(){return $this->belongsToModel('PatientType');}
    public function headDoctor(){return $this->morphTo();}
    public function modelHasService(){return $this->morphOneModel('ModelHasService','reference');}
    public function modelHasServices(){return $this->morphManyModel('ModelHasService','reference');}
    public function services(){
        return $this->belongsToManyModel(
            'Service','ModelHasService',
            'reference_id','service_id'
        )->where($this->ModelHasServiceModel()->getTable().'.reference_type',$this->getMorphClass());
    }
    public function referral(){return $this->belongsToModel('Referral');}

    public static array $activityList = [
        Activity::POLI_EXAM->value.'_'.ActivityStatus::POLI_EXAM_QUEUE->value         => ['flag' => 'POLI_EXAM_QUEUE', 'message' => 'Pasien menunggu pemeriksaan'],
        Activity::POLI_EXAM->value.'_'.ActivityStatus::POLI_EXAM_START->value         => ['flag' => 'POLI_EXAM_START', 'message' => 'Pemeriksaan dimulai'],
        Activity::POLI_EXAM->value.'_'.ActivityStatus::POLI_EXAM_END->value           => ['flag' => 'POLI_EXAM_END', 'message' => 'Pemeriksaan selesai'],
        Activity::POLI_SESSION->value.'_'.ActivityStatus::POLI_SESSION_START->value   => ['flag' => 'POLI_SESSION_START', 'message' => 'Sesi pemeriksaan dibuka'],
        Activity::POLI_SESSION->value.'_'.ActivityStatus::POLI_SESSION_END->value     => ['flag' => 'POLI_SESSION_END', 'message' => 'Sesi pemeriksaan ditutup'],
        Activity::POLI_SESSION->value.'_'.ActivityStatus::POLI_SESSION_CANCEL->value  => ['flag' => 'POLI_SESSION_CANCEL', 'message' => 'Pemeriksaan Dibatalkan'],
    ];
}
