<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\InternalReferralData;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\InternalReferral
 * @method self setParamLogic(string $logic, bool $search_value = false, ?array $optionals = [])
 * @method self conditionals(mixed $conditionals)
 * @method bool deleteInternalReferral()
 * @method bool prepareDeleteInternalReferral(? array $attributes = null)
 * @method mixed getInternalReferral()
 * @method ?Model prepareShowInternalReferral(?Model $model = null, ?array $attributes = null)
 * @method array showInternalReferral(?Model $model = null)
 * @method Collection prepareViewInternalReferralList()
 * @method array viewInternalReferralList()
 * @method LengthAwarePaginator prepareViewInternalReferralPaginate(PaginateData $paginate_dto)
 * @method array viewInternalReferralPaginate(?PaginateData $paginate_dto = null)
 * @method array storeInternalReferral(?InternalReferralData $internal_referral_dto = null)
 * @method Builder internalReferral(mixed $conditionals = null)
 */
interface InternalReferral extends DataManagement {
    // public function prepareStoreInternalReferral(InternalReferralData $internal_referral_dto): Model;
}
