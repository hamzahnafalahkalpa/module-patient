<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\{
    Models\BaseModel,
    Concerns\Support\HasActivity
};
use Hanafalah\ModuleMedicService\Enums\Label;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Hanafalah\ModulePatient\Enums\{
    VisitExamination\ExaminationStatus,
    VisitRegistration\Status
};
use Hanafalah\ModulePatient\Enums\VisitExamination\Activity;
use Hanafalah\ModulePatient\Enums\VisitExamination\ActivityStatus;
use Hanafalah\ModulePatient\Concerns\HasPractitionerEvaluation;
use Hanafalah\ModuleExamination\Concerns\HasExaminationSummary;
use Hanafalah\ModulePatient\Resources\VisitExamination\ShowVisitExamination;
use Hanafalah\ModulePatient\Resources\VisitExamination\ViewVisitExamination;

class VisitExamination extends BaseModel
{
    use HasUlids, SoftDeletes, HasProps, HasActivity;
    use HasExaminationSummary, HasPractitionerEvaluation;

    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    protected $list       = [
        'id',
        'visit_examination_code',
        'visit_patient_id',
        'visit_registration_id',
        'patient_id',
        'is_commit',
        'status',
        'props'
    ];
    protected $show       = [];

    protected $casts = [
        'created_at'   => 'date'
    ];

    protected static function booted(): void
    {
        parent::booted();
        static::creating(function ($query) {
            if (!isset($query->visit_examination_code)) {
                $query->visit_examination_code = static::hasEncoding('VISIT_EXAMINATION');
            }
            if (!isset($query->status)) {
                $visit_registration = $query->visitRegistration()->find($query->visit_registration_id);
                $service_validation = in_array($visit_registration->medicService->flag, [
                    Label::OUTPATIENT->value,
                    Label::MCU->value,
                    Label::EMERGENCY_UNIT->value
                ]);
                if ($service_validation) $query->status = ExaminationStatus::VISITING->value;

                //PURPOSE INPATIENT CONDITION
                if (!$service_validation) $query->status = ExaminationStatus::DRAFT->value;
            }
        });
        static::updated(function ($query) {
            $dirtyStatus    = $query->isDirty('status');
            if ($dirtyStatus) {
                $originalStatus = $query->getOriginal('status');

                //FROM VISITING OR DRAFT TO EXAMING
                $toExaming = $originalStatus !== ExaminationStatus::EXAMING->value && $query->status === ExaminationStatus::EXAMING->value;
                if ($toExaming) {
                    $visitReg = $query->visitRegistration()->find($query->visit_registration_id);
                    $visitReg->status = Status::PROCESSING->value;
                    $visitReg->saveQuitely();
                }
            }

            //WHEN DELETING
            $dirtyDeletedAt = $query->isDirty('deleted_at');
            if ($dirtyDeletedAt && $query->deleted_at) {
                $query->status = ExaminationStatus::CANCELLED->value;
                $query->saveQuitely();
            }
        });
    }

    public function viewUsingRelation(): array{
        return [];
    }

    public function showUsingRelation(): array{
        return ['visitRegistration','practitionerEvaluations'];
    }

    public function getViewResource(){return ViewVisitExamination::class;}
    public function getShowResource(){return ShowVisitExamination::class;}

    public function visitPatient(){return $this->belongsToModel('VisitPatient');}
    public function visitRegistration(){return $this->belongsToModel('VisitRegistration');}
    public function patientType(){return $this->belongsToModel('PatientType');}
    public function examinationTreatments(){return $this->hasManyModel('ExaminationTreatment');}
    public function assessments(){return $this->morphManyModel('Assessment','examination');}
    public function pharmacySale(){return $this->morphOneModel('PharmacySale', 'reference');}
    public function pharmacySales(){return $this->morphMany('PharmacySale', 'reference');}
    public function examinationSummary(){return $this->morphOneModel('ExaminationSummary', 'reference');}

    public array $activityList = [
        Activity::VISITATION->value . '_' . ActivityStatus::VISIT_CREATED->value  => ['flag' => 'VISIT_CREATED', 'message' => 'Data kunjungan dibuat'],
        Activity::VISITATION->value . '_' . ActivityStatus::VISITING->value       => ['flag' => 'VISITING', 'message' => 'Kunjungan dilakukan'],
        Activity::VISITATION->value . '_' . ActivityStatus::VISITED->value        => ['flag' => 'VISITED', 'message' => 'Kunjungan selesai'],
        Activity::VISITATION->value . '_' . ActivityStatus::CANCELLED->value      => ['flag' => 'CANCELLED', 'message' => 'Data Patient dibatalkan']
    ];
}
