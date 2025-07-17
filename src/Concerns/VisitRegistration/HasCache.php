<?php

namespace Hanafalah\ModulePatient\Concerns\VisitRegistration;

use Hanafalah\LaravelSupport\Concerns\PackageManagement\HasCacheConfiguration;

trait HasCache
{
    use HasCacheConfiguration;

    protected array $__cache = [
        'index' => [
            'name'     => 'visit-registration',
            'tags'     => ['visit-registration', 'visit-registration-index'],
            'forever'  => true
        ],
        'show' => [
            'name'     => 'visit-registration-show',
            'tags'     => ['visit-registration', 'visit-registration-show'],
            'forever'  => true
        ]
    ];
}
