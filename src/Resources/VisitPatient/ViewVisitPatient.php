<?php

namespace Hanafalah\ModulePatient\Resources\VisitPatient;

use Hanafalah\LaravelSupport\Resources\ApiResource;
use Hanafalah\ModuleTransaction\Resources\Transaction\ShowTransaction;

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
            'transaction'        => $this->prop_transaction,
            "reservation_id"     => $this->reservation_id,
            "queue_number"       => $this->queue_number,
            "flag"               => $this->flag,
            "visited_at"         => $this->visited_at,
            "reported_at"        => $this->reported_at,
            "referral"           => $this->prop_referral,
            "reference"          => $this->relationValidation('reference', function () {
                return $this->reference->toViewApi()->resolve();
            }),
            "status"             => $this->status,
            "payer"              => $this->prop_payer,
            "agent"              => $this->prop_agent,
            "organization"       => $this->relationValidation("organization", function () {
                return $this->organization->toViewApi()->resolve();
            }),
            'visit_registration'  => $this->prop_visit_registration,
            "visit_registrations" => $this->relationValidation("visitRegistrations", function () {
                return $this->visitRegistrations->transform(function ($visitRegistration) {
                    return $visitRegistration->toViewApi();
                });
            }),
            "services"           => $this->relationValidation('services', function () {
                $services = $this->services;
                return $services->map(function ($service) {
                    $arr = ['id' => $service->getKey()];
                    if (isset($service->name)) $arr['name'] = $service->name;
                    return $arr;
                });
            }),
            'patient'            => $this->prop_patient,
            'properties'         => $this->properties ?? null,
            'activity'           => $this->prop_activity ?? null,
            "created_at"         => $this->created_at,
            "updated_at"         => $this->updated_at,
        ];

        return $arr;
    }
}
