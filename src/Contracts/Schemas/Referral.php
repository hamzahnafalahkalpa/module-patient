<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\ReferralData;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\Referral
 * @method self setParamLogic(string $logic, bool $search_value = false, ?array $optionals = [])
 * @method self conditionals(mixed $conditionals)
 * @method bool deleteReferral()
 * @method bool prepareDeleteReferral(? array $attributes = null)
 * @method mixed getReferral()
 * @method ?Model prepareShowReferral(?Model $model = null, ?array $attributes = null)
 * @method array showReferral(?Model $model = null)
 * @method Collection prepareViewReferralList()
 * @method array viewReferralList()
 * @method LengthAwarePaginator prepareViewReferralPaginate(PaginateData $paginate_dto)
 * @method array viewReferralPaginate(?PaginateData $paginate_dto = null)
 * @method array storeReferral(?ReferralData $referral_dto = null)
 * @method Builder referral(mixed $conditionals = null)
 */
interface Referral extends DataManagement {
    // public function prepareStoreReferral(ReferralData $referral_dto): Model;
}
