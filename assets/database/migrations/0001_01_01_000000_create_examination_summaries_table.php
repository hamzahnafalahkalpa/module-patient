<?php

use Zahzah\ModulePatient\Models\EMR\ExaminationSummary;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use Zahzah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ExaminationSummary', ExaminationSummary::class));
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
                $table->ulid('id')->collation("utf8mb4_bin")->primary();
                $table->string("reference_type", 50)->nullable(false);
                $table->string("reference_id", 36)->nullable(false);
                $table->json('props')->nullable();
                $table->timestamps();

                $table->index(['reference_type','reference_id'],'ref_es');
            });

            Schema::table($table_name,function (Blueprint $table){
                $table->foreignIdFor($this->__table,'parent_id')->collation("utf8mb4_bin")
                        ->nullable()->after($this->__table->getKeyName())
                        ->index()->constrained()->cascadeOnDelete()->cascadeOnUpdate();

                $table->foreignIdFor($this->__table,'group_summary_id')->collation("utf8mb4_bin")
                        ->nullable()->after('parent_id')
                        ->index()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
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
