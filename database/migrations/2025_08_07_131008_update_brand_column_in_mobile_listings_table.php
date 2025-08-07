<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBrandColumnInMobileListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mobile_listings', function (Blueprint $table) {
            $table->renameColumn('brand', 'brand_id');
            $table->renameColumn('model', 'model_id');
        });

          Schema::table('mobile_listings', function (Blueprint $table) {
            $table->unsignedInteger('brand_id')->change();
            $table->unsignedInteger('model_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobile_listings', function (Blueprint $table) {
           $table->string('brand_id')->change();
           $table->string('model_id')->change();
        });

        Schema::table('mobile_listings', function (Blueprint $table) {
            $table->renameColumn('brand_id', 'brand');
            $table->renameColumn('model_id', 'model');
        });
    }
}
