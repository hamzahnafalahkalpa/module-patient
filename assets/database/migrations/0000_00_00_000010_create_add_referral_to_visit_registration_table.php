<?php

use Hanafalah\ModuleMedicService\Models\MedicService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePatient\Enums\EvaluationEmployee\Commit;
use Hanafalah\ModulePatient\Models\{
    Emr\VisitRegistration,
    Emr\VisitPatient,
    Patient\PatientType,
};
use Hanafalah\ModulePatient\Models\EMR\Referral;

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.VisitRegistration', VisitRegistration::class));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        if (!$this->isTableExists()) {
            Schema::create($table_name, function (Blueprint $table) {
                $patient_type  = app(config('database.models.PatientType', PatientType::class));
                $visit_patient = app(config('database.models.VisitPatient', VisitPatient::class));
                $medic_service = app(config('database.models.MedicService', MedicService::class));
                $referral      = app(config('database.models.Referral', Referral::class));

                $table->ulid('id')->primary();
                $table->string('visit_registration_code', 100)->nullable();
                $table->foreignIdFor($visit_patient::class)
                    ->nullable(false)->index()
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->foreignIdFor($patient_type::class)
                    ->nullable(true)->index()
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->foreignIdFor($medic_service::class)
                    ->nullable(false)->index()
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->string('head_doctor_type', 50)->nullable(true);
                $table->string('head_doctor_id', 36)->nullable(true);

                $table->foreignIdFor($referral::class)
                    ->nullable()->index()->constrained()
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->string('status', 50)->nullable(true);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['head_doctor_id', 'head_doctor_type'], 'vr_head_doctor');
            });

            Schema::table($table_name, function (Blueprint $table) {
                $table->foreignIdFor($this->__table::class, 'parent_id')
                    ->nullable()->after('id')
                    ->index()->constrained()
                    ->cascadeOnUpdate()->restrictOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->__table->getTable());
    }
};
