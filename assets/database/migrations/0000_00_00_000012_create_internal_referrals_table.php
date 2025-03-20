<?php

use Gii\ModuleMedicService\Models\MedicService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Zahzah\ModulePatient\Models\EMR\InternalReferral;

return new class extends Migration
{
   use Zahzah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct(){
        $this->__table = app(config('database.models.InternalReferral', InternalReferral::class));
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
                $medic_service = app(config('database.models.MedicService',MedicService::class));

                $table->ulid('id')->collation('utf8mb4_bin')->primary();
                $table->foreignIdFor($medic_service::class)->nullable(false)
                      ->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();
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
