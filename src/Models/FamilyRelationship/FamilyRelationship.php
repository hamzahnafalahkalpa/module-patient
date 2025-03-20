<?php

namespace Hanafalah\ModulePatient\Models\FamilyRelationship;

use Hanafalah\ModulePeople\Models\FamilyRelationship\FamilyRelationship as PeopleFamilyRelationship;

class FamilyRelationship extends PeopleFamilyRelationship
{
    protected $list = ['id', 'patient_id', 'people_id', 'name', 'phone', 'role', 'reference_id', 'reference_type', 'props'];
    public function patient()
    {
        return $this->belongsToModel('Patient');
    }
}
