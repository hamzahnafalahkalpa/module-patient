<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Zahzah\LaravelSupport\Concerns\NowYouSeeMe;
use Zahzah\ModulePatient\Enums\FamilyRelationship\Role;
use Zahzah\ModulePatient\Models\FamilyRelationship\FamilyRelationship;
use Zahzah\ModulePatient\Models\{
    Patient\Patient
};
use Zahzah\ModulePeople\Models\People\People;

return new class extends Migration
{
    use NowYouSeeMe;

    private $__table,$__table_patient,$__table_people;

    public function __construct(){
        $this->__table = app(config('database.models.FamilyRelationship', FamilyRelationship::class));
        $this->__table_patient = app(config('database.models.Patient', Patient::class));
        $this->__table_people = app(config('database.models.People', People::class));
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
                $table->id();
                $table->string('name',50)->nullable(true);
                $table->string('phone',50)->nullable(true);
                $table->enum('role', array_column(Role::cases(), 'value'));
                $table->string('reference_type',50)->nullable(true);
                $table->string('reference_id',36)->nullable(true);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['reference_type','reference_id']);
            });

            Schema::table($table_name,function (Blueprint $table) use ($table_name){
                $table->foreignIdFor($this->__table_patient,'patient_id')->nullable()->after('id')->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignIdFor($this->__table_people,'people_id')->nullable()->after('id')->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();
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
