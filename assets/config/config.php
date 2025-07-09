<?php

use Hanafalah\ModulePatient\{
    Commands as ModulePatientCommands,
};
use Hanafalah\ModulePatient\Enums\Patient\CardIdentity;

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
    'patient_identities' => CardIdentity::cases(),
    'patient_types' => [
        //THIS KEY SAME WITH MODEL NAME USING SNAKE CASE
        'people' => [
            'schema' => 'PatientPeople',
        ], 
        'animal' => [
            'schema' => null,
        ]
    ],
    'commands' => [
        ModulePatientCommands\InstallMakeCommand::class
    ],
    'practitioner' => 'User'
];
