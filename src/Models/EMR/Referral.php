<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Concerns\Support\HasActivity;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModulePatient\Enums\Referral\Status;
use Hanafalah\ModulePatient\Enums\VisitRegistration\Activity;
use Hanafalah\ModulePatient\Enums\VisitRegistration\ActivityStatus;
use Hanafalah\ModulePatient\Resources\Referral\ShowReferral;
use Hanafalah\ModulePatient\Resources\Referral\ViewReferral;

class Referral extends BaseModel
{
    use HasUlids, HasProps, SoftDeletes, HasActivity;

    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    protected $list       = [
        'id', 'referral_code', 'referral_type', 'medic_service_id', 
        'visit_type', 'visit_id', 'status', 'props'
    ];
    protected $show       = [];

    protected $casts = [
        'referral_code'  => 'string',
        'created_at'     => 'date',
        'name'           => 'string',
        'dob'            => 'date',
        'nik'            => 'string',
        'medical_record' => 'string'
    ];

    public function getPropsQuery(): array
    {
        return [
            'name'           => 'props->prop_patient->prop_people->name',
            'dob'            => 'props->prop_patient->prop_people->dob',
            'nik'            => 'props->prop_patient->nik',
            'medical_record' => 'props->prop_patient->medical_record'
        ];
    }

    protected static function booted(): void{
        parent::booted();
        static::creating(function ($query) {
            $query->referral_code ??= static::hasEncoding('REFERRAL');
            $query->status        ??= $query->getStatus('CREATED');
        });
    }

    public function getStatus(string $status){
        return Status::from($status)->value;
    }

    public function getViewResource(){return ViewReferral::class;}
    public function getShowResource(){return ShowReferral::class;}

    public function reference(){return $this->morphTo();}
    public function visit(){return $this->morphTo();}
    public function visitRegistrations(){return $this->hasManyModel('VisitRegistration');}
    public function medicService(){return $this->belongsToModel('MedicService');}

    public array $activityList = [
        Activity::REFERRAL_POLI->value . '_' . ActivityStatus::REFERRAL_CREATED->value    => ['flag' => 'REFERRAL_CREATED',   'message' => 'Pembuatan data Rujukan.'],
        Activity::REFERRAL_POLI->value . '_' . ActivityStatus::REFERRAL_PROCESSED->value  => ['flag' => 'REFERRAL_PROCESSED', 'message' => 'Rujukan di sedang dalam proses.'],
        Activity::REFERRAL_POLI->value . '_' . ActivityStatus::REFERRAL_DONE->value       => ['flag' => 'REFERRAL_DONE',      'message' => 'Rujukan Telah selesai di lakukan.'],
    ];
}
