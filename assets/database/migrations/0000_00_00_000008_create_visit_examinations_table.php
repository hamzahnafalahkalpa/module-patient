<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePatient\Enums\EvaluationEmployee\Commit;
use Hanafalah\ModulePatient\Enums\VisitExamination\ExaminationStatus;
use Hanafalah\ModulePatient\Models\{
    EMR\VisitExamination,
    EMR\VisitRegistration,
};
use Hanafalah\ModulePatient\Models\EMR\VisitPatient;

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.VisitExamination', VisitExamination::class));
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
                $visit_patient = app(config('database.models.VisitPatient', VisitPatient::class));
                $visit_registration = app(config('database.models.VisitRegistration', VisitRegistration::class));

                $table->ulid('id')->primary();
                $table->string('visit_examination_code', 100)->nullable();
                $table->foreignIdFor($visit_registration::class)
                        ->nullable(false)->index()
                        ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor($visit_patient::class)
                        ->nullable(false)->index()
                        ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->boolean('is_commit')->default(0)->nullable(false);
                $table->enum('status', array_column(ExaminationStatus::cases(), 'value'));
                $table->json('props')->nullable();
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
