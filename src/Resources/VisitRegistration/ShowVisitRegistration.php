<?php

namespace Hanafalah\ModulePatient\Resources\VisitRegistration;

class ShowVisitRegistration extends ViewVisitRegistration
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'services'            => $this->relationValidation('services', function () {
                return $this->services->transform(function ($service) {
                    return $service->toViewApi();
                });
            }),
            'visit_registrations' => $this->relationValidation('visitRegistrations', function () {
                return $this->visitRegistrations->transform(function ($visitRegistration) {
                    return $visitRegistration->toShowApi();
                });
            })
        ];
        $arr = $this->mergeArray(parent::toArray($request), $arr);
        return $arr;
    }
}
