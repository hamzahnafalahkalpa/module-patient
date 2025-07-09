<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePatient\Models\EMR\VisitExamination;
use Hanafalah\ModulePatient\Enums\EvaluationEmployee\Commit;
use Hanafalah\ModulePatient\Models\{
    EMR\PractitionerEvaluation,
};
use Hanafalah\ModulePatient\Models\EMR\VisitRegistration;
use Hanafalah\ModuleProfession\Models\Profession\Profession;

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
                $profession = app(config('database.models.Profession', Profession::class));

                $table->ulid('id')->primary();
                $table->string('name', 100)->default('')->nullable(false);

                $table->string('reference_type', 50)->nullable(false);
                $table->string('reference_id', 36)->nullable(false);
                $table->string('practitioner_type', 50)->nullable(true);
                $table->string('practitioner_id', 36)->nullable(true);

                $table->boolean('is_commit')->default(Commit::DRAFT->value)->nullable(false);
                $table->foreignIdFor($profession)
                    ->constrained($profession->getTable(), $profession->getKeyName(), 'pf_pe_fk')
                    ->cascadeOnUpdate()->restrictOnDelete();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['reference_type', 'reference_id'], 'practitioner_ref');
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
