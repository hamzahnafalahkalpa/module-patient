<?php

namespace Hanafalah\ModulePatient\Supports;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Supports\PackageManagement;

class BaseModulePatient extends PackageManagement implements DataManagement
{
    protected $__config_name = 'module-patient';
    protected $__module_patient_config = [];

    /**
     * A description of the entire PHP function.
     *
     * @param Container $app The Container instance
     * @throws Exception description of exception
     * @return void
     */
    public function __construct()
    {
        $this->setConfig('module-patient', $this->__module_patient_config);
    }
}
