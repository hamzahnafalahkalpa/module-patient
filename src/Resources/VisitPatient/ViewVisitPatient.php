<?php

namespace Zahzah\ModulePatient\Resources\VisitPatient;

use Zahzah\LaravelSupport\Resources\ApiResource;
use Zahzah\ModuleTransaction\Resources\Transaction\ShowTransaction;

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
            'transaction' => $this->relationValidation('transaction',function(){
                return $this->transaction->toViewApi();
            }),
            "reservation_id"     => $this->reservation_id,
            "queue_number"       => $this->queue_number,
            "flag"               => $this->flag,
            "visited_at"         => $this->visited_at,
            "reported_at"        => $this->reported_at,
            "reference"          => $this->relationValidation('reference',function(){
                return $this->reference->toViewApi();
            }),
            "status"             => $this->status,
            "payer"       => $this->relationValidation("payer", function () {
                return $this->payer->toViewApi();
            }),
            "agent"       => $this->relationValidation("agent", function () {
                return $this->agent->toViewApi();
            }),
            "organization"       => $this->relationValidation("organization", function () {
                return $this->organization->toViewApi();
            }),
            "visit_registration" => $this->relationValidation("visitRegistration", function () {
                $this->visitRegistration->load("medicService");
                return $this->visitRegistration->toViewApi();
            }),
            "visit_registrations" => $this->relationValidation("visitRegistrations", function () {
                return $this->visitRegistrations->transform(function($visitRegistration){
                    return $visitRegistration->toViewApi();
                });
            }),
            "services"           => $this->relationValidation('services',function(){
                $services = $this->services;
                return $services->map(function($service){
                    $arr = ['id' => $service->getKey()];
                    if (isset($service->name)) $arr['name'] = $service->name;
                    return $arr;
                });
            }),
            'patient' => $this->relationValidation('patient',function(){
                // return $this->patient->toShowApi();
                $patient = $this->patient;
                $arr = [
                    'id'     => $patient->getKey(),
                    'card_identities' => $patient->cardIdentities->mapWithKeys(function ($cardIdentity) {
                        return [$cardIdentity->flag => $cardIdentity->value];
                    }),
                    'user_reference' => [
                        'uuid' => $patient->uuid
                    ]
                ];
                if (class_exists(\Zahzah\ModulePeople\Models\People\People::class)) {
                    if ($patient->reference_type == $this->PeopleModel()->getMorphClass()){
                        $arr['people'] = $patient->propResource($patient->reference,\Zahzah\ModulePeople\Resources\People\ViewPeople::class);
                    }
                }
                return $arr;
            }),
            'properties'         => $this->properties ?? null,
            'activity'           => $this->prop_activity ?? null,
            "created_at"         => $this->created_at,
            "updated_at"         => $this->updated_at,
        ];

        return $arr;
    }
}
