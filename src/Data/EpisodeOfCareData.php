<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\ModuleMonitoring\Data\MonitoringData;
use Hanafalah\ModulePatient\Contracts\Data\EpisodeOfCareData as DataEpisodeOfCareData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class EpisodeOfCareData extends MonitoringData implements DataEpisodeOfCareData
{
}