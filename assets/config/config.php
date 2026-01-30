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
        'view' => 'Views',
        'config' => '../assets/database/config',
        'migration' => '../assets/database/migrations'
    ],
    'database' => [
        'models' => [
        ]
    ],
    'features' => [
        'payer' => true,
        'item_rent' => true,
        'payment_summary' => true,
    ],
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
        ]
    ],
    'patient_identities' => CardIdentity::cases(),
    'commands' => [
        InstallMakeCommand::class
    ],
    'filesystem' => [
        'profile_path' => 'profiles'
    ],
    'direct_referral_froms' => [
        //DIRECT REFERRAL FROM POLY
        'RADIOLOGY','PATOLOGI KLINIK','PATOLOGI ANATOMI'
    ],
    'practitioner' => 'User',
    'payment_detail' => 'PaymentDetail',
    'transaction' => 'Transaction',
    'exports' => [
        'VisitRegistrationEmr' => \Hanafalah\ModulePatient\Exports\VisitRegistrationEmrExport::class,
    ],
];
