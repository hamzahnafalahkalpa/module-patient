<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Zahzah\ModulePatient\Models\EMR\ExternalReferral;
use Zahzah\ModulePatient\Models\EMR\VisitPatient;

return new class extends Migration
{
   use Zahzah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct(){
        $this->__table = app(config('database.models.ExternalReferral',ExternalReferral::class));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        if (!$this->isTableExists()){
            Schema::create($table_name, function (Blueprint $table) {
                $visit_patient = app(config('database.models.VisitPatient',VisitPatient::class));

                $table->ulid('id')->collation('utf8mb4_bin')->primary();
                $table->foreignIdFor($visit_patient::class)
                      ->nullable(false)->constrained()
                      ->cascadeOnUpdate()
                      ->cascadeOnDelete();

                $table->date("date");
                $table->string("doctor_name");
                $table->string("phone")->nullable(true);
                $table->string("facility_name")->nullable(true);
                $table->string("unit_name")->nullable(true);
                $table->string("initial_diagnose");
                $table->string("note");
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
