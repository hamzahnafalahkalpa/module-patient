<?php

namespace Hanafalah\ModulePatient\Resources\Queue;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewQueuePatient extends ApiResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
   */
  public function toArray($request): array
  {
    $arr = [
      "id"                 => $this->id,
      "uuid"               => $this->uuid,
      "transaction_code"   => $this->transaction_code,
      "reference"          => $this->relationValidation("reference", function () {
        return $this->reference->toShowApi()->resolve();
      }),
    ];

    return $arr;
  }
}
