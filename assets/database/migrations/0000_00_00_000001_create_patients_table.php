<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePatient\Models\Patient\{
    Patient
};
use Hanafalah\ModuleService\Models\Service;
use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

return new class extends Migration
{
    use NowYouSeeMe;
    private $__table, $__table_service;

    public function __construct()
    {
        $this->__table = app(config('database.models.Patient', Patient::class));
        $this->__table_service = app(config('database.models.Service', Service::class));
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
                $table->id();
                $table->string('reference_type', 50)->nullable(false);
                $table->string('reference_id', 36)->nullable(false);
                $table->string('medical_record', 50)->nullable();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['reference_type', 'reference_id']);
            });

            Schema::table($table_name, function (Blueprint $table) use ($table_name) {
                $table->foreignIdFor($this->__table, 'central_patient_id')->nullable()->after('id')->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignIdFor($this->__table_service, 'patient_type_id')->nullable()->after('id')->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();
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
