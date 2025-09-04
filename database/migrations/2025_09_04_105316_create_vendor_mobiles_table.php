<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorMobilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_mobiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('brand_id')->nullable();
            $table->unsignedInteger('model_id')->nullable();
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->string('storage')->nullable();
            $table->string('ram')->nullable();
            $table->string('price')->nullable();
            $table->string('condition')->nullable();
            $table->string('processor')->nullable();
            $table->string('display')->nullable();
            $table->string('charging')->nullable();
            $table->string('refresh_rate')->nullable();
            $table->string('main_camera')->nullable();
            $table->string('ultra_camera')->nullable();
            $table->string('telephoto_camera')->nullable();
            $table->string('front_camera')->nullable();
            $table->string('build')->nullable();
            $table->string('wireless')->nullable();
            $table->string('stock')->nullable();
            $table->string('pta_approved')->nullable()->default(0);
            $table->string('ai_features')->nullable();
            $table->string('battery_health')->nullable();
            $table->string('os_version')->nullable();
            $table->string('warranty_start')->nullable();
            $table->string('warranty_end')->nullable();
            $table->string('image')->nullable();
            $table->longtext('about')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_mobiles');
    }
}
