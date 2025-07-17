<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Concerns\Support\HasProfilePhoto;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleCardIdentity\Concerns\HasCardIdentity;
use Hanafalah\ModulePatient\Enums\Patient\CardIdentity;
use Hanafalah\ModuleUser\Concerns\UserReference\HasUserReference;
use Hanafalah\ModuleRegional\Concerns\HasLocation;
use Hanafalah\ModulePatient\Resources\Patient\{
    ShowPatient,
    ViewPatient
};
use Hanafalah\ModuleEncoding\Concerns\HasEncoding;
use Hanafalah\ModulePayment\Concerns\HasDeposit;
use Hanafalah\ModulePeople\Resources\People\ViewPeople;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Patient extends BaseModel
{
    use HasUlids, HasProps, SoftDeletes,
        HasCardIdentity, HasUserReference,
        HasLocation, HasDeposit, 
        HasProfilePhoto; 

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    protected $list = [
        'id', 'uuid', 'name', 'reference_type', 'reference_id', 'medical_record', 
        'profile', 'patient_type_id', 'props'
    ];
    protected $show = [];

    protected $casts = [
        'name'                    => 'string',
        'first_name'              => 'string',
        'last_name'               => 'string',
        'dob'                     => 'immutable_date',
        'medical_record'          => 'string',
        'patient_occupation_name' => 'string',
        'payer_name'              => 'string'
    ];

    public function getPropsQuery(): array{return [
            'first_name'               => 'props->prop_people->first_name',
            'last_name'                => 'props->prop_people->last_name',
            'dob'                      => 'props->prop_people->dob',
            'patient_occupation_name'  => 'props->prop_patient_occupation->name',
            'payer_name'               => 'props->prop_payer->name'
        ];
    }

    protected $prop_attributes = [
        'People' => ViewPeople::class
    ];

    public static function booted(): void{parent::booted();
        static::creating(function ($query) {
            if (!isset($query->medical_record)) {
                $medical_record = HasEncoding::generateCode('MEDICAL_RECORD');
                $query->medical_record = $medical_record;
            }
        });
        static::created(function ($query) {
            if (isset($query->medical_record)) {
                $query->setCardIdentity(CardIdentity::MEDICAL_RECORD->value, $query->medical_record);
            }
            if (!isset($query->uuid)) {
                if (isset(tenancy()->tenant)) {
                    $tenant_id = \tenancy()->tenant->getKey();
                    $central_tenant_id = \tenancy()->tenant->parent_id;
                }
                $user_ref = $query->userReference()->firstOrCreate([
                    "reference_id"      => $query->getKey(),
                    "reference_type"    => $query->getMorphClass(),
                    "tenant_id"         => $tenant_id ?? null,
                    "central_tenant_id" => $central_tenant_id ?? null
                ]);
                $query->uuid = $user_ref->uuid;
                $query->save();
            }
            $query->load('reference');
            $query->patientSummary()->firstOrCreate([
                'patient_id'     => $query->getKey(),
                'reference_id'   => $query->reference_id,
                'reference_type' => $query->reference_type
            ]);
        });
    }

    public function viewUsingRelation(): array{
        return [];
    }

    public function showUsingRelation(): array{
        return [
            'reference' => function ($query) {
                $query->with('addresses', 'familyRelationship', 'hasPhones', 'cardIdentities', 'userReference');
            }
        ];
    }

    public function getViewResource(){return ViewPatient::class;}
    public function getShowResource(){return ShowPatient::class;}
    public function scopeUUID($builder, $uuid, $uuid_name = "props->uuid"){return $builder->where($uuid_name, $uuid);}
    public function patientType(){return $this->belongsToModel('PatientType');}
    public function people(){return $this->belongsToModel('People');}
    public function reference(){return $this->morphTo();}
    public function familyRelationships(){return $this->hasManyModel('FamilyRelationship');}
    public function familyRelationship(){return $this->hasOneModel('FamilyRelationship');}
    public function cardIdentity(){return $this->morphOneModel('CardIdentity', 'reference');}
    public function cardIdentities(){return $this->morphManyModel('CardIdentity', 'reference');}
    public function visitPatient(){return $this->hasOneModel('VisitPatient');}
    public function patientSummary(){return $this->hasOneModel('PatientSummary');}
    public function boat(){return $this->hasOneModel("ModelHasOrganization");}
    public function invoice(){return $this->morphOneModel('Invoice', 'consument');}
    public function modelHasOrganization(){return $this->morphOneModel('ModelHasOrganization','model');}
    public function payer(){
        return $this->morphOneModel('ModelHasOrganization','model');
    }
}
