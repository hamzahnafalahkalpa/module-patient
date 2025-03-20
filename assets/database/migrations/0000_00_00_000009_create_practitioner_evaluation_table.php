<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Projects\Klinik\Models\Patient\EMR\VisitExamination;
use Hanafalah\ModulePatient\Enums\EvaluationEmployee\Commit;
use Hanafalah\ModulePatient\Models\{
    Emr\PractitionerEvaluation,
};

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.PractitionerEvaluation', PractitionerEvaluation::class));
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
                $visitExamination = app(config('database.models.VisitExamination', VisitExamination::class));

                $table->ulid('id')->collation('utf8mb4_bin')->primary();
                $table->foreignIdFor($visitExamination)
                    ->constrained($visitExamination->getTable(), $visitExamination->getKeyName(), 've_pe_fk')
                    ->cascadeOnUpdate()->restrictOnDelete();

                $table->string('practitioner_type', 50)->nullable(true);
                $table->string('practitioner_id', 36)->nullable(true);
                $table->string('name', 100)->default('')->nullable(false);

                $table->boolean('is_commit')->default(Commit::DRAFT->value)->nullable(false);
                $table->stirng('role_as', 50)->nullable(false);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['practitioner_type', 'practitioner_id'], 'practitioner_pe_morph');
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
