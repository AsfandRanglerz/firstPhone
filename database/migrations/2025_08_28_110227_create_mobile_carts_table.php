<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobileCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

             $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('storage')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('condition')->nullable();
            $table->string('color')->nullable();
            $table->string('ram')->nullable();
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
            $table->string('ai_features')->nullable();
            $table->string('battery_health')->nullable();
            $table->string('os_version')->nullable();
            $table->date('warranty_start')->nullable();
            $table->date('warranty_end')->nullable();
            $table->enum('pta_approved', ['Approved', 'Not Approved'])->nullable();
            
            // Cart specific
            $table->integer('quantity');
            $table->text('images')->nullable();
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
        Schema::dropIfExists('mobile_carts');
    }
}
