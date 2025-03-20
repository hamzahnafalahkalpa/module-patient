<?php

namespace Zahzah\ModulePatient\Schemas;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Zahzah\ModulePatient\Contracts\FamilyRelationship as ContractsFamilyRelationship;
use Illuminate\Database\Eloquent\Builder;
use Zahzah\LaravelSupport\Supports\PackageManagement;

class FamilyRelationship extends PackageManagement implements ContractsFamilyRelationship {
    
    protected array $__guard   = ['id','patient_id','people_id']; 
    protected array $__add     = ['name','phone','role','patient_id','people_id'];
    protected string $__entity = 'FamilyRelationship';
    public function booting(): self{
        static::$__class = $this;
        static::$__model = $this->{$this->__entity."Model"}();
        return $this;
    }

    public function familyRelationship(mixed $conditionals = null): builder{
        return $this->getModel()->conditionals($conditionals);
    }

    public function getRelationships(){
      $datas =  $this->familyRelationship(function($query){
          
      })->paginate(request('per_page'))->appends(request()->all());
      return $datas;
    }

    public function addOrChange(? array $attributes=[]): self{    
        $this->updateOrCreate($attributes);
        return $this;
    }
}
