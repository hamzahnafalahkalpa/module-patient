<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Projects\Klinik\Models\Patient\EMR\VisitExamination;
use Zahzah\ModulePatient\Enums\EvaluationEmployee\Commit;
use Zahzah\ModulePatient\Models\{
    Emr\PractitionerEvaluation,
};
use Zahzah\ModulePatient\Models\EMR\Referral;
use Zahzah\ModulePatient\Models\EMR\VisitRegistration;

return new class extends Migration
{
   use Zahzah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct(){
        $this->__table = app(config('database.models.VisitRegistration', VisitRegistration::class));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        if (!$this->isColumnExists('referral_id')){
            Schema::table($table_name, function (Blueprint $table) {
                // $referral = app(config('database.models.Referral', Referral::class));

                $table->char('referral_id', 36)->collation('utf8mb4_bin')->after('status')
                ->nullable(true)->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();
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
