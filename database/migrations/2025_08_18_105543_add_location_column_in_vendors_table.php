<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationColumnInVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->unsignedInteger('brand_id')->nullable()->after('image');
            $table->unsignedInteger('model_id')->nullable()->after('brand_id');
            $table->string('location')->nullable()->after('model_id');
            $table->decimal('latitude', 10, 6)->nullable()->after('location');
            $table->decimal('longitude', 10, 6)->nullable()->after('latitude');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            //
        });
    }
}
