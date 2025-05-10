<?php

use Hanafalah\ModulePatient\{
    Commands as ModulePatientCommands,
};

return [
    'namespace' => 'Hanafalah\ModulePatient',
    'app' => [
        'contracts' => [
        ],
    ],
    'libs' => [
        'model' => 'Models',
        'contract' => 'Contracts',
        'schema' => 'Schemas',
        'database' => 'Database',
        'data' => 'Data',
        'resource' => 'Resources',
        'migration' => '../assets/database/migrations',
    ],
    'database' => [
        'models' => [
        ]
    ],
    'commands' => [
        ModulePatientCommands\InstallMakeCommand::class
    ],
    'practitioner' => \App\Models\User::class,
    'head_doctor'  => \App\Models\User::class
];
