<?php

use Hanafalah\ModuleMedicService\Models\MedicService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModulePatient\Models\EMR\Referral;

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
            Schema::create($table_name, function (Blueprint $table) {
                $medic_service = app(config('database.models.MedicService',MedicService::class));

                $table->ulid('id')->primary();
                $table->string('referral_code', 50)->nullable(false);
                $table->string('referral_type', 50)->nullable(false);
                $table->string('visit_type', 50)->nullable(false);
                $table->string('visit_id', 36)->nullable(false);
                $table->string('status', 50)->nullable(true);
                $table->foreignIdFor($medic_service::class)->index()->constrained()
                      ->cascadeOnUpdate()->restrictOnDelete();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['visit_type', 'visit_id'], 'visit_ref_mrph');
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
