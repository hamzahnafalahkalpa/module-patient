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
    'patient_types' => [
        'People', 'Animal'
    ],
    'commands' => [
        ModulePatientCommands\InstallMakeCommand::class
    ],
    'practitioner' => 'User',
    'head_doctor'  => 'User'
];
