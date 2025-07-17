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
    'practitioner' => 'User',
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
        ModuleExamination\Commands\InstallMakeCommand::class
    ],
];
