<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Illuminate\Database\Eloquent\SoftDeletes;
use Projects\Klinik\Enums\Encoding\EnumEncoding;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleCardIdentity\Concerns\HasCardIdentity;
use Hanafalah\ModulePatient\Enums\Patient\CardIdentity;
use Hanafalah\ModuleUser\Concerns\UserReference\HasUserReference;
use Hanafalah\ModuleRegional\Concerns\HasLocation;
use Hanafalah\LaravelSupport\Concerns\Support\HasEncoding;
use Hanafalah\ModulePatient\Resources\Patient\{
    ShowPatient,
    ViewPatient
};
use Hanafalah\ModuleTransaction\Concerns\HasDeposit;

class Patient extends BaseModel
{
    use HasProps,
        SoftDeletes,
        HasCardIdentity,
        HasUserReference,
        HasLocation,
        HasDeposit;

    protected $list = ['id', 'reference_type', 'reference_id', 'medical_record', 'patient_type_id', 'props'];
    protected $show = [];

    protected $identity_flags = [
        'MR',
        'BPJS_CODE',
        'OLD_MR',
        'NIK',
        'PASSPORT'
    ];

    protected $casts = [
        'name'           => 'string',
        'first_name'     => 'string',
        'last_name'      => 'string',
        'dob'            => 'immutable_date',
        'medical_record' => 'string'
    ];

    public function getPropsQuery(): array
    {
        return [
            'name'             => 'props->prop_people->name',
            'first_name'       => 'props->prop_people->first_name',
            'last_name'        => 'props->prop_people->last_name',
            'dob'              => 'props->prop_people->dob',
            'occupation_name'  => 'props->prop_occupation->name',
            'medical_record'   => 'props->medical_record'
        ];
    }

    protected $prop_attributes = [
        'People'            => [
            'id',
            'name',
            'first_name',
            'last_name',
            'dob',
            'pob',
            'email',
            'phone_number',
            'father_name',
            'marital_status',
            'nationality',
            'sex'
        ],
        'UserReference'     => ['uuid']
    ];

    public static function booted(): void
    {
        parent::booted();
        static::creating(function ($query) {
            if (!isset($query->medical_record)) {
                $medical_record = HasEncoding::generateCode(EnumEncoding::MEDICAL_RECORD->value);
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

    public function toViewApi()
    {
        return new ViewPatient($this);
    }

    public function toShowApi()
    {
        return new ShowPatient($this);
    }

    public function scopeUUID($builder, $uuid, $uuid_name = "props->uuid")
    {
        return $builder->where($uuid_name, $uuid);
    }

    public function getIdentityFlags(): array
    {
        return $this->identity_flags;
    }

    public function patientType()
    {
        return $this->belongsToModel('PatientType');
    }
    public function people()
    {
        return $this->belongsToModel('People');
    }
    public function reference()
    {
        return $this->morphTo();
    }
    public function familyRelationships()
    {
        return $this->hasManyModel('FamilyRelationship');
    }
    public function familyRelationship()
    {
        return $this->hasOneModel('FamilyRelationship');
    }
    public function cardIdentity()
    {
        return $this->morphOneModel('CardIdentity', 'reference');
    }
    public function cardIdentities()
    {
        return $this->morphManyModel('CardIdentity', 'reference');
    }
    public function visitPatient()
    {
        return $this->hasOneModel('VisitPatient');
    }
    public function patientSummary()
    {
        return $this->hasOneModel('PatientSummary');
    }
    public function boat()
    {
        return $this->hasOneModel("ModelHasOrganization");
    }
    public function invoice()
    {
        return $this->morphOneModel('Invoice', 'consument');
    }
}
