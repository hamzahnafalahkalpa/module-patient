<?php

namespace Hanafalah\ModulePatient\Schemas;

use Illuminate\Database\Eloquent\Builder;
use Hanafalah\ModuleMonitoring\Schemas\Monitoring;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModulePatient\Contracts\Schemas\EpisodeOfCare as ContractsEpisodeOfCare;
use Hanafalah\ModulePatient\Contracts\Data\EpisodeOfCareData;

class EpisodeOfCare extends Monitoring implements ContractsEpisodeOfCare
{
    protected string $__entity = 'EpisodeOfCare';
    public $episode_of_care_model;
    //protected mixed $__order_by_created_at = false; //asc, desc, false

    protected array $__cache = [
        'index' => [
            'name'     => 'episode_of_care',
            'tags'     => ['episode_of_care', 'episode_of_care-index'],
            'duration' => 24 * 60
        ]
    ];

    public function prepareStoreEpisodeOfCare(EpisodeOfCareData $episode_of_care_dto): Model{
        $episode_of_care = parent::prepareStoreMonitoring($episode_of_care_dto);
        return $this->episode_of_care_model = $episode_of_care;
    }

    public function episodeOfCare(mixed $conditionals = null): Builder{
        return $this->monitoring($conditionals);
    }
}