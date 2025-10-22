<?php

namespace Hanafalah\ModulePatient\Resources\VisitExamination;

use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewVisitExamination extends ApiResource
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
            'id'                     => $this->id,
            'visit_examination_code' => $this->visit_examination_code,
            'examination'            => $this->examination ?? null,
            'visit_registration_id'  => $this->visit_registration_id,

            // 'visit_registration'     => $this->relationValidation('visitRegistration',function(){
            //     return $this->visitRegistration->toViewApi()->resolve();
            // }),            
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
        ];
        return $arr;
    }
}
