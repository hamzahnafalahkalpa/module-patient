<?php

use Hanafalah\ModuleMedicService\Models\MedicService;
use Hanafalah\ModuleMedicService\Models\ServiceCluster;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePatient\Models\{
    EMR\VisitRegistration,
};
use Hanafalah\ModulePatient\Models\Patient\PatientTypeService;

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
                $medic_service        = app(config('database.models.MedicService', MedicService::class));
                $service_cluster      = app(config('database.models.ServiceCluster', ServiceCluster::class));

                $table->ulid('id')->primary();
                $table->string('visit_registration_code', 100)->nullable();
                $table->string('visit_patient_type', 50)->nullable(false);
                $table->string('visit_patient_id', 36)->nullable(false);

                $table->string('warehouse_type', 50)->nullable();
                $table->string('warehouse_id', 36)->nullable();

                $table->foreignIdFor($medic_service::class)
                    ->nullable(true)->index()
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->foreignIdFor($service_cluster::class)
                    ->nullable(true)->index()
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->string('status', 50)->nullable(true);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['visit_patient_id', 'visit_patient_type'], 'vr_visit_ref');
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
