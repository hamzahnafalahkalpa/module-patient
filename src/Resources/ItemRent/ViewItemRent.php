<?php

namespace Hanafalah\ModulePatient\Resources\ItemRent;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewItemRent extends ApiResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
   */
  public function toArray(\Illuminate\Http\Request $request): array
  {
    $arr = [
      'id'             => $this->id, 
      'reference_type' => $this->reference_type, 
      'reference_id'   => $this->reference_id, 
      'flag'           => $this->flag,
      'service_id'     => $this->service_id, 
      'service'        => $this->prop_service,
      'asset_type'     => $this->asset_type, 
      'asset_id'       => $this->asset_id,
      'asset'          => $this->prop_asset
    ];
    return $arr;
  }
}
