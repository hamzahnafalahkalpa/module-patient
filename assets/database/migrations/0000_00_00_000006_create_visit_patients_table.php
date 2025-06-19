<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\MicroTenant\Models\Tenant\Tenant;
use Hanafalah\ModulePatient\Enums\VisitPatient\VisitStatus;
use Hanafalah\ModulePatient\Models\{
    EMR\VisitPatient,
    Patient\Patient
};
use Hanafalah\ModulePatient\Models\Patient\PatientTypeService;

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.VisitPatient', VisitPatient::class));
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
                $patient = app(config('database.models.Patient', Patient::class));
                $tenant  = app(config('database.models.Tenant', Tenant::class));
                $patient_type_service  = app(config('database.models.PatientTypeService', PatientTypeService::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($patient::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->string('reference_type', 50)->nullable();
                $table->string('reference_id', 36)->nullable();
                $table->string('flag', 60)->nullable(false);
                $table->string('visit_code', 100)->nullable();
                $table->string('reservation_id', 36)->nullable();
                $table->foreignIdFor($patient_type_service::class)->nullable(false)
                      ->index()->constrained($patient_type_service->getTable(),'patient_type_service_id','pts_vp')
                      ->cascadeOnUpdate()->restrictOnDelete();
                $table->string('queue_number', 10)->nullable();
                $table->timestamp('visited_at');
                $table->timestamp('reported_at')->nullable();
                $table->foreignIdFor($tenant::class)
                    ->nullable()->index()->constrained()
                    ->cascadeOnUpdate()->restrictOnDelete();
                $table->enum('status', array_column(VisitStatus::cases(), 'value'));
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::table($table_name, function (Blueprint $table) use ($table_name) {
                $table->foreignIdFor($this->__table::class, 'parent_id')->nullable()->after('id')->index()
                    ->constrained($table_name, $this->__table->getKeyName())
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
