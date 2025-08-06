<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobileRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_requests', function (Blueprint $table) {
            $table->id();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('storage')->nullable();
            $table->string('ram')->nullable();
            $table->string('color')->nullable();
            $table->string('condition')->nullable();
            $table->string('default')->default(2)->nullable();
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
        Schema::dropIfExists('mobile_requests');
    }
}
