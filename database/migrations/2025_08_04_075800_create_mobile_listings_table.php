<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobileListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_listings', function (Blueprint $table) {
            $table->id();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('storage')->nullable();
            $table->string('ram')->nullable();
            $table->string('price')->nullable();
            $table->string('condition')->nullable();
            $table->string('image')->nullable();
            $table->longtext('about')->nullable();
            $table->string('status')->default(2)->nullable();
            $table->string('action')->default(1)->nullable();
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
        Schema::dropIfExists('mobile_listings');
    }
}
