<?php

declare(strict_types=1);

namespace Hanafalah\ModulePatient;

use Hanafalah\LaravelSupport\Providers\BaseServiceProvider;

class ModulePatientServiceProvider extends BaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return $this
     */
    public function register()
    {
        $this->registerMainClass(ModulePatient::class)
            ->registerCommandService(Providers\CommandServiceProvider::class)
            ->registers([
                '*',
                'Services' => function () {
                    $this->binds([
                        Contracts\ModulePatient::class => ModulePatient::class,
                        Contracts\Patient::class => Schemas\Patient::class,
                        Contracts\PatientType::class => Schemas\PatientType::class,
                        Contracts\FamilyRelationship::class => Schemas\FamilyRelationship::class,
                        Contracts\ModulePatient::class => ModulePatient::class,
                        Contracts\Patient::class => Schemas\Patient::class,
                        Contracts\PatientType::class => Schemas\PatientType::class,
                        Contracts\FamilyRelationship::class => Schemas\FamilyRelationship::class,
                        Contracts\VisitPatient::class => Schemas\VisitPatient::class,
                        Contracts\VisitRegistration::class => Schemas\VisitRegistration::class,
                        Contracts\PractitionerEvaluation::class => Schemas\PractitionerEvaluation::class,
                        Contracts\VisitExamination::class => Schemas\VisitExamination::class,
                        Contracts\InternalReferral::class => Schemas\InternalReferral::class,
                        Contracts\Referral::class => Schemas\Referral::class,
                        Contracts\ExternalReferral::class => Schemas\ExternalReferral::class,
                        Contracts\ExaminationSummary::class => Schemas\ExaminationSummary::class,
                    ]);
                }
            ]);
    }

    /**
     * Get the base path of the package.
     *
     * @return string
     */
    protected function dir(): string
    {
        return __DIR__ . '/';
    }

    protected function migrationPath(string $path = ''): string
    {
        return database_path($path);
    }
}
