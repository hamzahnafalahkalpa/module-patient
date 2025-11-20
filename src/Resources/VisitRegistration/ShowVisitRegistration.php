<?php

namespace Hanafalah\ModulePatient\Resources\VisitRegistration;

class ShowVisitRegistration extends ViewVisitRegistration
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'visit_patient'       => $this->relationValidation('visitPatient',function(){
                return $this->visitPatient->toViewApi();
            },$this->prop_visit_patient),
            'services'            => $this->relationValidation('services', function () {
                return $this->services->transform(function ($service) {
                    return $service->toViewApi();
                });
            }),
            'payment_summary' => $this->relationValidation('paymentSummary', function () {
                return $this->paymentSummary->toShowApi()->resolve();
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
