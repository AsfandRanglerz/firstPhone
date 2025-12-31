<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsOrderedColumnsToMobileCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mobile_carts', function (Blueprint $table) {
            $table->tinyInteger('is_ordered')->default(0)->after('mobile_listing_id')->nullable();
            $table->string('quantity')->after('is_ordered')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobile_carts', function (Blueprint $table) {
            $table->dropColumn(['is_ordered', 'quantity']);
        });
    }
}
