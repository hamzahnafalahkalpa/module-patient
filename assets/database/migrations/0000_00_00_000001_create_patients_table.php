<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePatient\Models\Patient\{
    Patient,
    PatientOccupation,
    PatientType
};
use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

return new class extends Migration
{
    use NowYouSeeMe;
    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Patient', Patient::class));
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
                $patient_type = app(config('database.models.PatientType', PatientType::class));
                $patient_occupation = app(config('database.models.PatientOccupation', PatientOccupation::class));

                $table->ulid('id')->primary();
                $table->string('uuid')->nullable();
                $table->string('name', 100)->nullable(false);
                $table->string('reference_type', 50)->nullable(false);
                $table->string('reference_id', 36)->nullable(false);
                $table->string('medical_record', 50)->nullable();
                $table->string('profile',255)->nullable();
                $table->foreignIdFor($patient_type)->nullable()->index()->constrained()
                    ->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignIdFor($patient_occupation)->nullable()->index()->constrained()
                    ->cascadeOnUpdate()->restrictOnDelete();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                $table->index(['reference_type', 'reference_id']);
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
