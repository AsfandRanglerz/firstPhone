<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinPriceColumnInMobileRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mobile_requests', function (Blueprint $table) {
            $table->decimal('min_price', 10, 2)->nullable()->after('model_id');
            $table->decimal('max_price', 10, 2)->nullable()->after('min_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobile_requests', function (Blueprint $table) {
            $table->dropColumn(['min_price', 'max_price']);
        });
    }
}
