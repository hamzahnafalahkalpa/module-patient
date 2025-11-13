<?php

namespace Hanafalah\ModulePatient\Contracts\Schemas;

use Hanafalah\ModulePatient\Contracts\Data\EpisodeOfCareData;
//use Hanafalah\ModulePatient\Contracts\Data\EpisodeOfCareUpdateData;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModuleMonitoring\Contracts\Schemas\Monitoring;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePatient\Schemas\EpisodeOfCare
 * @method mixed export(string $type)
 * @method self conditionals(mixed $conditionals)
 * @method array updateEpisodeOfCare(?EpisodeOfCareData $episode_of_care_dto = null)
 * @method Model prepareUpdateEpisodeOfCare(EpisodeOfCareData $episode_of_care_dto)
 * @method bool deleteEpisodeOfCare()
 * @method bool prepareDeleteEpisodeOfCare(? array $attributes = null)
 * @method mixed getEpisodeOfCare()
 * @method ?Model prepareShowEpisodeOfCare(?Model $model = null, ?array $attributes = null)
 * @method array showEpisodeOfCare(?Model $model = null)
 * @method Collection prepareViewEpisodeOfCareList()
 * @method array viewEpisodeOfCareList()
 * @method LengthAwarePaginator prepareViewEpisodeOfCarePaginate(PaginateData $paginate_dto)
 * @method array viewEpisodeOfCarePaginate(?PaginateData $paginate_dto = null)
 * @method array storeEpisodeOfCare(?EpisodeOfCareData $episode_of_care_dto = null)
 * @method Collection prepareStoreMultipleEpisodeOfCare(array $datas)
 * @method array storeMultipleEpisodeOfCare(array $datas)
 */

interface EpisodeOfCare extends Monitoring
{
    public function prepareStoreEpisodeOfCare(EpisodeOfCareData $episode_of_care_dto): Model;
}