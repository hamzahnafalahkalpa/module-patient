<?php

use Hanafalah\ModulePatient\Models\EMR\ItemRent;
use Hanafalah\ModuleService\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.ItemRent', ItemRent::class));
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
                $service = app(config('database.models.Service', Service::class));

                $table->ulid('id')->primary();
                $table->string('reference_type',50);
                $table->string('reference_id',36);
                $table->string('flag',100)->nullable(false);
                $table->foreignIdFor($service::class)->index()->constrained()
                      ->cascadeOnUpdate()->restrictOnDelete();
                $table->string('asset_type',50)->nullable();
                $table->string('asset_id',36)->nullable();

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
