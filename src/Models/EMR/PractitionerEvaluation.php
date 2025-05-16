<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModulePatient\Enums\EvaluationEmployee\Commit;
use Hanafalah\ModulePatient\Enums\EvaluationEmployee\PIC;
use Hanafalah\ModulePatient\Resources\PractitionerEvaluation\ShowPractitionerEvaluation;
use Hanafalah\ModulePatient\Resources\PractitionerEvaluation\ViewPractitionerEvaluation;

class PractitionerEvaluation extends BaseModel
{
    use HasUlids, HasProps, SoftDeletes;

    //IS COMMIT LOOK ENUM
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    protected $list       = [
        'id',
        'visit_examination_id',
        'practitioner_type',
        'practitioner_id',
        'name',
        'is_commit',
        'role_as',
        'props'
    ];

    protected $casts = [
        'name' => 'string'
    ];

    public function getPropsQuery(): array
    {
        return ['name' => 'props->prop_people->name'];
    }

    protected static function booted(): void
    {
        parent::booted();
        static::creating(function ($query) {
            if (!isset($query->is_commit)) $query->is_commit = Commit::DRAFT->value;
        });
    }

    public function getViewResource(){
        return ViewPractitionerEvaluation::class;
    }

    public function getShowResource(){
        return ShowPractitionerEvaluation::class;
    }

    public function viewUsingRelation(): array{
        return [];
    }

    public function showUsingRelation(): array{
        return ['practitioner'];
    }

    //SCOPE SECTION
    public function scopeCommit($builder)
    {
        return $builder->where('is_commit', Commit::COMMIT->value);
    }
    public function scopeDraft($builder)
    {
        return $builder->where('is_commit', Commit::DRAFT->value);
    }
    public function scopePic($builder)
    {
        return $builder->where('role_as', PIC::IS_PIC->value);
    }
    public function scopeDoctor($builder)
    {
        return $builder->where('role_as', PIC::IS_DOCTOR->value);
    }
    public function scopeMidwife($builder)
    {
        return $builder->where('role_as', PIC::IS_MIDWIFE->value);
    }
    public function scopeNurse($builder)
    {
        return $builder->where('role_as', PIC::IS_NURSE->value);
    }
    public function scopeOther($builder)
    {
        return $builder->where('role_as', PIC::IS_OTHER->value);
    }

    //EIGER SECTION
    public function practitioner()
    {
        return $this->morphTo();
    }
    public function visitExamination()
    {
        return $this->belongsToModel('VisitExamination');
    }
}
