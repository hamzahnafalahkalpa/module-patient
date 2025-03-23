<?php

use Hanafalah\ModulePatient\{
    Models as ModulePatientModels,
    Commands as ModulePatientCommands,
    Contracts
};

use Hanafalah\ModuleExamination\{
    Contracts as ModuleExaminationContracts,
    Contracts\Examination as ExaminationContracts,
    Contracts\Examination\Assessment as AssessmentContracts,
    Contracts\Examination\Assessment\Treatment as TreatmentContracts,
};
use Hanafalah\ModulePatient\Contracts\Patient;
use Hanafalah\ModulePharmacy\Contracts as ModulePharmacyContracts;
use Hanafalah\ModuleTransaction\Contracts as ModuleTransactionContracts;
use Hanafalah\ModuleTransaction\Contracts\TransactionItem;

return [
    'commands' => [
        ModulePatientCommands\InstallMakeCommand::class
    ],
    'app' => [
        'contracts' => [
            //ADD YOUR CONTRACTS HERE
            'transaction'             => ModuleTransactionContracts\Transaction::class,
            'external_referral'       => Contracts\ExternalReferral::class,
            'practitioner_evaluation' => Contracts\PractitionerEvaluation::class,
            'visit_examination'       => Contracts\VisitExamination::class,
            'visit_registration'      => Contracts\VisitRegistration::class,
            'visit_patient'           => Contracts\VisitPatient::class,
            'internal_referral'       => Contracts\InternalReferral::class,
            'pharmacy_sale'           => ModulePharmacyContracts\PharmacySale::class,
            'assessment'              => AssessmentContracts\Assessment::class,
            'examination'             => ModuleExaminationContracts\Examination::class,
            'examination_treatment'   => ExaminationContracts\ExaminationTreatment::class,
            'examination_summary'     => Contracts\ExaminationSummary::class,
            'clinical_treatment'      => TreatmentContracts\ClinicalTreatment::class,
            'radiology_treatment'     => TreatmentContracts\RadiologyTreatment::class,
            'lab_treatment'           => TreatmentContracts\LabTreatment::class,
            'transaction_item'        => TransactionItem::class,
            'patient'                 => Patient::class
        ],
    ],
    'libs' => [
        'model' => 'Models',
        'contract' => 'Contracts'
    ],
    'database' => [
        'models' => [
            'Patient'                   => ModulePatientModels\Patient\Patient::class,
            'ExaminationSummary'        => ModulePatientModels\EMR\ExaminationSummary::class,
            'PatientSummary'            => ModulePatientModels\EMR\PatientSummary::class,
            'PractitionerEvaluation'    => ModulePatientModels\EMR\PractitionerEvaluation::class,
            'VisitExamination'          => ModulePatientModels\EMR\VisitExamination::class,
            'VisitRegistration'         => ModulePatientModels\EMR\VisitRegistration::class,
            'VisitPatient'              => ModulePatientModels\EMR\VisitPatient::class,
            'Referral'                  => ModulePatientModels\EMR\Referral::class,
            'InternalReferral'          => ModulePatientModels\EMR\InternalReferral::class,
            'ExternalReferral'          => ModulePatientModels\EMR\ExternalReferral::class,
            'PatientType'               => ModulePatientModels\Patient\PatientType::class,
            'FamilyRelationship'        => ModulePatientModels\FamilyRelationship\FamilyRelationship::class,
            'PatientTypeHistory'        => ModulePatientModels\Patient\PatientTypeHistory::class
        ]
    ],
    'practitioner' => \App\Models\User::class,
    'head_doctor'  => \App\Models\User::class
];
