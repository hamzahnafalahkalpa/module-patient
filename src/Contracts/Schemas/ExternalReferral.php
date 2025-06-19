<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModulePatient\Contracts\Data\ExternalReferralData;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\ExternalReferral
 * @method self setParamLogic(string $logic, bool $search_value = false, ?array $optionals = [])
 * @method self conditionals(mixed $conditionals)
 * @method bool deleteExternalReferral()
 * @method bool prepareDeleteExternalReferral(? array $attributes = null)
 * @method mixed getExternalReferral()
 * @method ?Model prepareShowExternalReferral(?Model $model = null, ?array $attributes = null)
 * @method array showExternalReferral(?Model $model = null)
 * @method Collection prepareViewExternalReferralList()
 * @method array viewExternalReferralList()
 * @method LengthAwarePaginator prepareViewExternalReferralPaginate(PaginateData $paginate_dto)
 * @method array viewExternalReferralPaginate(?PaginateData $paginate_dto = null)
 * @method array storeExternalReferral(?ExternalReferralData $external_referral_dto = null)
 * @method Builder externalReferral(mixed $conditionals = null)
 */
interface ExternalReferral extends DataManagement {
    // public function prepareStoreExternalReferral(ExternalReferralData $external_referral_dto): Model;
}
