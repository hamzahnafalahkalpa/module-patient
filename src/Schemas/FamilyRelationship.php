<?php

namespace Hanafalah\ModulePatient\Schemas;

use Hanafalah\ModulePatient\Contracts\Schemas\FamilyRelationship as ContractsFamilyRelationship;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModulePatient\Contracts\Data\FamilyRelationshipData;
use Illuminate\Database\Eloquent\Model;

class FamilyRelationship extends PackageManagement implements ContractsFamilyRelationship
{
    protected string $__entity = 'FamilyRelationship';
    public static $family_relationship;

    public function prepareStoreFamilyRelationship(FamilyRelationshipData $family_relationship_dto): Model{
        $model =  $this->usingEntity()->updateOrCreate([
            'id' => $family_relationship_dto->id ?? null
        ],[
            'name' => $family_relationship_dto->name
        ]);
        $this->fillingProps($model, $family_relationship_dto);
        $model->save();
        return static::$family_relationship = $model;
    }
}
