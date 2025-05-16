<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\FamilyRelationshipData;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\FamilyRelationship
 * @method self conditionals(mixed $conditionals)
 * @method bool deleteFamilyRelationship()
 * @method bool prepareDeleteFamilyRelationship(? array $attributes = null)
 * @method mixed getFamilyRelationship()
 * @method ?Model prepareShowFamilyRelationship(?Model $model = null, ?array $attributes = null)
 * @method array showFamilyRelationship(?Model $model = null)
 * @method Collection prepareViewFamilyRelationshipList()
 * @method array viewFamilyRelationshipList()
 * @method LengthAwarePaginator prepareViewFamilyRelationshipPaginate(PaginateData $paginate_dto)
 * @method array viewFamilyRelationshipPaginate(?PaginateData $paginate_dto = null)
 * @method array storeFamilyRelationship(?FamilyRelationshipData $family_relationship_dto = null)
 * @method Builder familyRelationship(mixed $conditionals = null)
 */
interface FamilyRelationship extends DataManagement {
    // public function prepareStoreFamilyRelationship(FamilyRelationshipData $family_relationship_dto): Model;
}
