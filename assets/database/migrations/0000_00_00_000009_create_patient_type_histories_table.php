<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePatient\Models\EMR\VisitPatient;
use Hanafalah\ModulePatient\Models\Patient\{
    PatientType,
    PatientTypeHistory
};

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.PatientTypeHistory', PatientTypeHistory::class));
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

                $table->ulid('id')->primary();
                $table->string('name')->nullable(false);
                $table->foreignIdFor($visit_patient::class)
                    ->nullable(false)->index()->constrained()
                    ->cascadeOnUpdate()->cascadeOnDelete();

                $table->foreignIdFor($patient_type::class)
                    ->nullable(false)->index()->constrained()
                    ->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
                $table->softDeletes();
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
