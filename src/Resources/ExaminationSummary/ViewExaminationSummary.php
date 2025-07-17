<?php

namespace Hanafalah\ModulePatient\Resources\ExaminationSummary;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewExaminationSummary extends ApiResource
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
            'id' => $this->id,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'props'      => $this->getPropsData() ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        return $arr;
    }
}
