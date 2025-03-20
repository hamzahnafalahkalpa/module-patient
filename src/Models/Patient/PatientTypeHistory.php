<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModulePatient\Resources\PatientTypeHistory\{ViewPatientTypeHistory, ShowPatientTypeHistory};

class PatientTypeHistory extends BaseModel
{
    use HasUlids, SoftDeletes;

    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    protected $list       = ['id', 'visit_patient_id', 'patient_type_id', 'name'];

    protected static function booted(): void
    {
        parent::booted();
        static::creating(function ($query) {
            $patient_type = app(config('database.models.PatientType', PatientType::class))->findOrFail($query->patient_type_id);
            $query->name = $patient_type->name;
        });
    }

    public function toViewApi()
    {
        return new ViewPatientTypeHistory($this);
    }

    public function toShowApi()
    {
        return new ShowPatientTypeHistory($this);
    }

    public function visitPatient()
    {
        return $this->belongsToModel('VisitPatient');
    }
    public function patientType()
    {
        return $this->belongsToModel('PatientType');
    }
}
