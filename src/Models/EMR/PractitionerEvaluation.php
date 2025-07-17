<?php

namespace Hanafalah\ModulePatient\Models\EMR;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModulePatient\Enums\EvaluationEmployee\Commit;
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
        'name',
        'reference_type',
        'reference_id',
        'practitioner_type',
        'practitioner_id',
        'profession_id',
        'is_commit',
        'props'
    ];

    protected $casts = [
        'name' => 'string',
        'pracitioner_name' => 'string'
    ];

    public function getPropsQuery(): array{
        return [
            'name' => 'props->prop_people->name',
            'practitioner_name' => 'props->prop_practitioner->name'
        ];
    }

    protected static function booted(): void{
        parent::booted();
        static::creating(function ($query) {
            $query->is_commit ??= Commit::DRAFT->value;
        });
    }

    public function getViewResource(){return ViewPractitionerEvaluation::class;}
    public function getShowResource(){return ShowPractitionerEvaluation::class;}
    public function viewUsingRelation(): array{
        return [];
    }

    public function showUsingRelation(): array{
        return ['practitioner'];
    }

    //SCOPE SECTION
    public function scopeCommit($builder){return $builder->where('is_commit', Commit::COMMIT->value);}
    public function scopeDraft($builder){return $builder->where('is_commit', Commit::DRAFT->value);}

    //EIGER SECTION
    public function practitioner(){return $this->morphTo();}
    public function profession(){return $this->belongsToModel('Profession');}
    public function reference(){return $this->morphTo();}
}
