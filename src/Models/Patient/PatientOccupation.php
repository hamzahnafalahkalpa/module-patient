<?php

namespace Hanafalah\ModulePatient\Models\Patient;

use Hanafalah\ModuleProfession\Models\Occupation\Occupation;

class PatientOccupation extends Occupation
{
    protected $table = 'professions';

    public static function getFlag(): string{
        return 'PatientOccupation';
    }
}
