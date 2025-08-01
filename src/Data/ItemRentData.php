<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\ItemRentData as DataItemRentData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class ItemRentData extends Data implements DataItemRentData
{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('reference_type')]
    #[MapName('reference_type')]
    public ?string $reference_type = null;

    #[MapInputName('reference_id')]
    #[MapName('reference_id')]
    public mixed $reference_id = null;

    #[MapInputName('reference_model')]
    #[MapName('reference_model')]
    public ?object $reference_model = null;

    #[MapInputName('flag')]
    #[MapName('flag')]
    public ?string $flag = null;

    #[MapInputName('service_id')]
    #[MapName('service_id')]
    public mixed $service_id;

    #[MapInputName('asset_type')]
    #[MapName('asset_type')]
    public string $asset_type;

    #[MapInputName('asset_id')]
    #[MapName('asset_id')]
    public mixed $asset_id;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;
}