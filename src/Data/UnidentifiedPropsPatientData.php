<?php

namespace Hanafalah\ModulePatient\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePatient\Contracts\Data\UnidentifiedPropsPatientData as DataUnidentifiedPropsPatientData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\In;

class UnidentifiedPropsPatientData extends Data implements DataUnidentifiedPropsPatientData
{
    #[MapInputName('sex')]
    #[MapName('sex')]
    #[In('Male','Famele','Other')]
    public ?string $sex = null;

    #[MapInputName('estimated_age')]
    #[MapName('estimated_age')]
    public ?array $estimatedAge = null;

    #[MapInputName('height')]
    #[MapName('height')]
    public ?int $height = null;

    #[MapInputName('weight')]
    #[MapName('weight')]
    public ?int $weight = null;

    #[MapInputName('skin_color')]
    #[MapName('skin_color')]
    public ?string $skinColor = null;

    #[MapInputName('hair')]
    #[MapName('hair')]
    public ?array $hair = null;

    #[MapInputName('facial_features')]
    #[MapName('facial_features')]
    public ?array $facialFeatures = null;

    #[MapInputName('clothing')]
    #[MapName('clothing')]
    public ?array $clothing = null;

    #[MapInputName('carried_items')]
    #[MapName('carried_items')]
    public ?array $carriedItems = null;

    #[MapInputName('medical_marks')]
    #[MapName('medical_marks')]
    public ?array $medicalMarks = null;

    #[MapInputName('blood_type')]
    #[MapName('blood_type')]
    public ?string $bloodType = null;

    #[MapInputName('language_response')]
    #[MapName('language_response')]
    public ?array $languageResponse = null;

    #[MapInputName('biometric_data')]
    #[MapName('biometric_data')]
    public ?array $biometricData = null;

    #[MapInputName('location_found')]
    #[MapName('location_found')]
    public ?string $locationFound = null;

    #[MapInputName('brought_by')]
    #[MapName('brought_by')]
    public ?string $broughtBy = null;

    #[MapInputName('reported_to_authorities')]
    #[MapName('reported_to_authorities')]
    public ?bool $reportedToAuthorities = null;

    #[MapInputName('notes')]
    #[MapName('notes')]
    public ?string $notes = null;

    #[MapInputName('created_by')]
    #[MapName('created_by')]
    public ?string $createdBy = null;

    public static function before(array &$attributes){
        if(!isset($attributes['estimated_age'])){
            $attributes['estimated_age'] = [
                'min_years' => null,
                'max_years' => null
            ];
        }
        if(!isset($attributes['hair'])){
            $attributes['hair'] = [
                'color' => null,
                'length' => null,
                'texture' => null
            ];
        }
        if(!isset($attributes['facial_features'])){
            $attributes['facial_features'] = [
                'beard' => null,
                'mustache' => null,
                'face_shape' => null
            ];
        }
        if(!isset($attributes['clothing'])){
            $attributes['clothing'] = [
                'upper' => null,
                'lower' => null,
                'footwear' => null,
                'accessories' => [],
                'labels_or_badges' => []
            ];
        }
        if(!isset($attributes['carried_items'])){
            $attributes['carried_items'] = [
                'wallet' => null,
                'phone' => null,
                'other_items' => [
                ]
            ];
        }
        if(!isset($attributes['medical_marks'])){
            $attributes['medical_marks'] = [
                'tattoos' => [],
                'surgical_scars' => [],
                'birthmarks' => [],
                'prosthetics' => null
            ];
        }
        if(!isset($attributes['language_response'])){
            $attributes['language_response'] = [
                'spoken_language' => null,
                'local_accent' => null,
                'other_languages_understood' => null
            ];
        }
        if(!isset($attributes['biometric_data'])){
            $attributes['biometric_data'] = [
                'fingerprints_taken' => null,
                'facial_photo_taken' => null,
                'retina_scan' => null,
                'voice_recording' => null
            ];
        }
    }
}
