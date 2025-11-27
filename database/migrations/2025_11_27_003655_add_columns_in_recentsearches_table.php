<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInRecentsearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recentsearches', function (Blueprint $table) {
            $table->unsignedBigInteger('brand_id')->nullable()->after('model');
            $table->unsignedBigInteger('model_id')->nullable()->after('brand_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recentsearches', function (Blueprint $table) {
            $table->dropColumn('brand_id');
            $table->dropColumn('model_id');
        });
    }
}
