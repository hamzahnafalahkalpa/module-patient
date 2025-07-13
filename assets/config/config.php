<?php

use Hanafalah\ModulePatient\{
    Commands as ModulePatientCommands,
};

use Hanafalah\ModuleExamination;

return [
    'namespace' => 'Hanafalah\\ModuleExamination',
    'app' => [
        'contracts' => [
            //ADD YOUR CONTRACTS HERE
        ]
    ],
    'libs' => [
        'model' => 'Models',
        'contract' => 'Contracts',
        'schema' => 'Schemas',
        'database' => 'Database',
        'data' => 'Data',
        'resource' => 'Resources',
        'migration' => '../assets/database/migrations'
    ],
    'database' => [
        'models' => [
        ]
    ],
    'practitioner' => \App\Models\User::class,
    'head_doctor'  => \App\Models\User::class,
    'commands' => [
        ModuleExamination\Commands\InstallMakeCommand::class
    ],
];
