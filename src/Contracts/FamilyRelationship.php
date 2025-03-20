<?php

namespace Hanafalah\ModulePatient\Contracts;

use Hanafalah\LaravelSupport\Contracts\DataManagement;
use Illuminate\Contracts\Database\Eloquent\Builder;

interface FamilyRelationship extends DataManagement
{
    public function booting(): self;
    public function familyRelationship(mixed $conditionals = null): Builder;
    public function getRelationships();
    public function addOrChange(?array $attributes = []): self;
}
