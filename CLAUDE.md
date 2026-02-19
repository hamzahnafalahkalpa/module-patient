# CLAUDE.md - Module Patient

This file provides guidance to Claude Code when working with this module.

## Overview

`hanafalah/module-patient` is a comprehensive patient management module for the Wellmed healthcare system. It handles:

- Patient registration and profile management
- Visit registration and tracking
- Medical record management
- Episode of care tracking
- Referral management
- Patient type classification
- Practitioner evaluations

**Namespace:** `Hanafalah\ModulePatient`

## CRITICAL: ServiceProvider Configuration

### WARNING: Do NOT use `registers(['*'])`

The current `ModulePatientServiceProvider` uses `registers(['*'])` which can cause memory exhaustion issues. This pattern is discouraged.

```php
// CURRENT (problematic)
public function register()
{
    $this->registerMainClass(ModulePatient::class)
        ->registerCommandService(Providers\CommandServiceProvider::class)
        ->registers(['*']);  // Can cause memory issues
}
```

**Recommended Pattern:**

```php
// SAFE - explicit registration
public function register()
{
    $this->registerMainClass(ModulePatient::class)
        ->registerCommandService(Providers\CommandServiceProvider::class);

    // Register services manually with closures for deferred loading
    $this->app->singleton(MyService::class, fn() => new MyService());
}
```

If you must use `registers()`, explicitly specify only what you need:
```php
->registers(['Config', 'Model', 'Database', 'Migration', 'Route'])
```

**Never explicitly add `'Schema'` or `'Services'` to `registers()` unless absolutely necessary** - these are the root cause of memory exhaustion chains.

See `/var/www/projects/wellmed/repositories/laravel-support/CLAUDE.md` for detailed explanation of memory issues.

## Dependencies

This module depends on:
- `hanafalah/laravel-support` - Base package
- `hanafalah/module-user` - User management
- `hanafalah/module-card-identity` - Identity card management
- `hanafalah/module-service` - Service management
- `hanafalah/module-medic-service` - Medical service management
- `hanafalah/module-examination` - Examination management
- `hanafalah/module-profession` - Profession management
- `hanafalah/module-transaction` - Transaction management
- `hanafalah/module-payment` - Payment management

## Directory Structure

```
module-patient/
├── src/
│   ├── ModulePatientServiceProvider.php  # Service provider
│   ├── ModulePatient.php                 # Main module class
│   ├── helper.php                        # Global helper functions
│   ├── Models/
│   │   ├── Patient/                      # Patient domain models
│   │   │   ├── Patient.php
│   │   │   ├── PatientType.php
│   │   │   ├── PatientTypeService.php
│   │   │   ├── PatientOccupation.php
│   │   │   ├── PatientTypeHistory.php
│   │   │   └── UnidentifiedPatient.php
│   │   └── EMR/                          # EMR (Electronic Medical Record) models
│   │       ├── VisitPatient.php
│   │       ├── VisitRegistration.php
│   │       ├── VisitExamination.php
│   │       ├── EpisodeOfCare.php
│   │       ├── Referral.php
│   │       ├── PractitionerEvaluation.php
│   │       ├── ExaminationSummary.php
│   │       ├── PatientSummary.php
│   │       ├── PatientDischarge.php
│   │       ├── ItemRent.php
│   │       └── OldVisit.php
│   ├── Schemas/                          # Business logic schemas
│   ├── Data/                             # DTOs (Data Transfer Objects)
│   ├── Contracts/                        # Interfaces
│   ├── Enums/                            # Enumerations
│   ├── Resources/                        # API Resources
│   ├── Concerns/                         # Traits
│   ├── Commands/                         # Artisan commands
│   ├── Providers/                        # Sub-providers
│   ├── Supports/                         # Support classes
│   └── Views/                            # Blade views
├── assets/
│   └── config/
│       └── config.php                    # Module configuration
└── composer.json
```

## Key Models

### Patient Domain (`Models/Patient/`)

| Model | Purpose |
|-------|---------|
| `Patient` | Core patient entity with medical record number |
| `PatientType` | Patient classification (e.g., regular, VIP) |
| `PatientTypeService` | Service-specific patient type configuration |
| `PatientOccupation` | Patient's occupation |
| `UnidentifiedPatient` | Temporary patients without full identification |

### EMR Domain (`Models/EMR/`)

| Model | Purpose |
|-------|---------|
| `VisitPatient` | Patient visit record (admission) |
| `VisitRegistration` | Service registration within a visit |
| `VisitExamination` | Examination during visit registration |
| `EpisodeOfCare` | Episode of care tracking |
| `Referral` | Patient referral management |
| `PractitionerEvaluation` | Doctor/practitioner evaluations |
| `ExaminationSummary` | Summary of examinations |
| `ItemRent` | Medical equipment rental tracking |

## Key Schemas (Business Logic)

Schemas contain the core business logic. Located in `src/Schemas/`:

| Schema | Purpose |
|--------|---------|
| `Patient` | Patient CRUD operations, profile management |
| `VisitPatient` | Visit admission workflow |
| `VisitRegistration` | Service registration workflow |
| `VisitExamination` | Examination workflow |
| `Referral` | Referral management |
| `PatientType` | Patient type management |
| `PractitionerEvaluation` | Practitioner evaluation logic |

## Enums

### Visit Status (`Enums/VisitPatient/VisitStatus.php`)
```php
case ACTIVE     = 'ACTIVE';
case CANCELLED  = 'CANCELLED';
case COMPLETED  = 'COMPLETED';
```

### Registration Status (`Enums/VisitRegistration/Status.php`)
```php
case DRAFT      = 'DRAFT';
case PROCESSING = 'PROCESSING';
case COMPLETED  = 'COMPLETED';
case CANCELLED  = 'CANCELLED';
```

### Activity Types

Visit and Registration activities are tracked using Activity and ActivityStatus enums:
- `VisitPatient/Activity.php` - ADM_VISIT, PATIENT_LIFE_CYCLE
- `VisitPatient/ActivityStatus.php` - ADM_START, ADM_PROCESSED, ADM_FINISHED, ADM_CANCELLED
- `VisitRegistration/Activity.php` - POLI_EXAM, POLI_SESSION
- `VisitRegistration/ActivityStatus.php` - POLI_EXAM_QUEUE, POLI_EXAM_START, POLI_EXAM_END, etc.

## Configuration

Configuration file: `assets/config/config.php`

Key configuration options:

```php
'features' => [
    'payer' => true,           // Enable payer/insurance support
    'item_rent' => true,       // Enable item rental tracking
    'payment_summary' => true, // Enable payment summary
],

'patient_types' => [
    'people' => ['schema' => 'PatientPeople'],
    'unidentified_patient' => ['schema' => 'UnidentifiedPatient'],
    'animal' => ['schema' => null],  // For veterinary use
],
```

## Usage Patterns

### Creating a Patient

```php
use Hanafalah\ModulePatient\Contracts\Data\PatientData;

$patientSchema = app(\Hanafalah\ModulePatient\Contracts\Schemas\Patient::class);

$patientData = $this->requestDTO(PatientData::class, [
    'name' => 'John Doe',
    'reference_type' => 'People',
    'reference' => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'dob' => '1990-01-01',
    ],
    'card_identity' => [
        'nik' => '1234567890123456',
    ],
]);

$patient = $patientSchema->prepareStorePatient($patientData);
```

### Creating a Visit

```php
use Hanafalah\ModulePatient\Contracts\Data\VisitPatientData;

$visitSchema = app(\Hanafalah\ModulePatient\Contracts\Schemas\VisitPatient::class);

$visitData = $this->requestDTO(VisitPatientData::class, [
    'patient_id' => $patient->id,
    'patient_type_service_id' => $serviceId,
    'visit_registration' => [
        'medic_service_id' => $medicServiceId,
        'warehouse_id' => $warehouseId,
    ],
]);

$visit = $visitSchema->prepareStoreVisitPatient($visitData);
```

### Helper Functions

The module provides global helper functions in `src/helper.php`:

```php
// Generate asset URL (S3 or local)
$url = asset_url('path/to/file.jpg');

// Generate profile photo URL
$photoUrl = profile_photo('photo.jpg');
```

## Model Relationships

### Patient Model
- `reference()` - Polymorphic to People or UnidentifiedPatient
- `patientType()` - Patient type classification
- `visitPatient()` - Current visit
- `cardIdentities()` - Identity cards (NIK, passport, etc.)
- `payer()` - Insurance/payer through ModelHasOrganization

### VisitPatient Model
- `patient()` - The patient being visited
- `visitRegistrations()` - All service registrations
- `transaction()` - Financial transaction
- `paymentSummary()` - Payment tracking
- `practitionerEvaluation()` - Admitting practitioner

### VisitRegistration Model
- `visitPatient()` - Parent visit
- `visitExamination()` - Examination record
- `medicService()` - Medical service
- `warehouse()` - Service location
- `itemRents()` - Rented items
- `practitionerEvaluations()` - Examining practitioners

## Traits (Concerns)

Key traits provided by this module:

| Trait | Purpose |
|-------|---------|
| `HasPatient` | Adds patient relationship to models |
| `HasPractitionerEvaluation` | Adds practitioner evaluation support |
| `HasExaminationSummary` | Adds examination summary support |

## API Resources

Resources for API responses in `src/Resources/`:

- `Patient/ViewPatient.php`, `Patient/ShowPatient.php`
- `VisitPatient/ViewVisitPatient.php`, `VisitPatient/ShowVisitPatient.php`
- `VisitRegistration/ViewVisitRegistration.php`, `VisitRegistration/ShowVisitRegistration.php`

Each model has View (list) and Show (detail) resources.

## Caching

Schemas implement caching for index operations:

```php
protected array $__cache = [
    'index' => [
        'name'     => 'patient',
        'tags'     => ['patient', 'patient-index'],
        'duration' => 3*24*60  // 3 days in minutes
    ]
];
```

## Testing Changes

After modifying this module:

```bash
# Clear caches
docker exec -it wellmed-backbone php artisan cache:clear
docker exec -it wellmed-backbone php artisan config:clear

# Reload Octane
docker exec -it wellmed-backbone php artisan octane:reload

# Run tests if available
docker exec -it wellmed-backbone php artisan test --filter=Patient
```

## Common Issues

### Memory Exhaustion
If you encounter memory issues during boot, check if `registers(['*'])` is being used and replace with explicit registration.

### Tenant Isolation
Patient data is tenant-specific. Ensure proper tenant context when querying patients.

### Medical Record Generation
Medical records are auto-generated using `HasEncoding::generateCode('MEDICAL_RECORD')`. Ensure encoding configuration is set up.

### Visit Status Transitions
Visit status changes trigger cascading updates to visit registrations. Be aware of the `updated` event in `VisitPatient` model.
