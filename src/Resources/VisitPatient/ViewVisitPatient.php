<?php

namespace Hanafalah\ModulePatient\Resources\VisitPatient;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewVisitPatient extends ApiResource
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
            "id"                 => $this->id,
            'visit_code'         => $this->visit_code,
            "reservation_id"     => $this->reservation_id,
            "queue_number"       => $this->queue_number,
            "flag"               => $this->flag,
            "visited_at"         => $this->visited_at,
            "reported_at"        => $this->reported_at,
            "referral"           => $this->propOnlies($this->prop_referral,'id','referral_code','external_referral'),
            "reference"          => $this->relationValidation('reference', function () {
                return $this->reference->toViewApi()->resolve();
            }),
            "status"             => $this->status,
            "payer_id"           => $this->payer_id,
            "payer"              => $this->relationValidation("payer", function () {
                return $this->payer->toShowApi()->resolve();
            },$this->prop_payer),
            "agent_id"           => $this->agent_id,
            "agent"              => $this->relationValidation("agent", function () {
                return $this->agent->toShowApi()->resolve();
            },$this->prop_agent),
            "organization"       => $this->relationValidation("organization", function () {
                return $this->organization->toViewApi()->resolve();
            }),
            'visit_registration'  => $this->prop_visit_registration,            
            "services"           => $this->relationValidation('services', function () {
                $services = $this->services;
                return $services->map(function ($service) {
                    $arr = ['id' => $service->getKey()];
                    if (isset($service->name)) $arr['name'] = $service->name;
                    return $arr;
                });
            }),
            'patient'            => $this->prop_patient,
            'activity'           => $this->prop_activity ?? null,
            "created_at"         => $this->created_at,
            "updated_at"         => $this->updated_at,
        ];

        return $arr;
    }
}
