<?php


use Hanafalah\ModulePatient\{
    Enums\Patient\CardIdentity,
    Commands\InstallMakeCommand
};

return [
    'namespace' => 'Hanafalah\ModulePatient',
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
        'config' => '../assets/database/config',
        'migration' => '../assets/database/migrations'
    ],
    'database' => [
        'models' => [
        ]
    ],
    'practitioner' => 'User',
    'patient_types' => [
        //THIS KEY SAME WITH MODEL NAME USING SNAKE CASE
        'people' => [
            'schema' => 'PatientPeople',
        ],
        'unidentified_patient' => [
            'schema' => 'UnidentifiedPatient'
        ], 
        'animal' => [
            'schema' => null,
        ],
        'vehicle' => [
            'schema' => null
        ]
    ],
    'patient_identities' => CardIdentity::cases(),
    'commands' => [
        InstallMakeCommand::class
    ],
];
