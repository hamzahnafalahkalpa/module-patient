<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Projects\Klinik\Models\Patient\EMR\VisitExamination;
use Hanafalah\ModulePatient\Enums\EvaluationEmployee\Commit;
use Hanafalah\ModulePatient\Models\{
    EMR\PractitionerEvaluation,
};
use Hanafalah\ModulePatient\Models\EMR\Referral;
use Hanafalah\ModulePatient\Models\EMR\VisitRegistration;

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Referral', Referral::class));
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
            $visit_registration = app(config('database.models.VisitRegistration', VisitRegistration::class));
            Schema::create($table_name, function (Blueprint $table) use ($visit_registration) {
                $table->ulid('id')->primary();
                $table->string('referral_code', 50)->nullable(false);
                $table->string('reference_type', 50)->nullable(false);
                $table->string('reference_id', 36)->nullable(false);
                $table->foreignIdFor($visit_registration::class)->nullable(false)
                    ->index()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->json('props')->nullable();
                $table->string('status', 50)->nullable(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['reference_type', 'reference_id'], 'referral_ref');
            });

            Schema::table($visit_registration->getTable(), function (Blueprint $table) use ($visit_registration) {
                $table->foreignIdFor($visit_registration::class)
                    ->nullable()->index()->constrained()
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
