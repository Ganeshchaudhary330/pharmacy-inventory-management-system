<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('sale_type', ['cash', 'credit'])->default('cash')->after('payment_method');
            $table->decimal('due_amount', 10, 2)->default(0)->after('change_amount');
            $table->date('due_date')->nullable()->after('due_amount');
            $table->dateTime('paid_at')->nullable()->after('due_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['sale_type', 'due_amount', 'due_date', 'paid_at']);
        });
    }
};
