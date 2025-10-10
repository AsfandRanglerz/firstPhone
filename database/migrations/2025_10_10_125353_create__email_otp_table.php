<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailOtpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_otp', function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary Key
            $table->string('name', 255)->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('email', 255)->index(); // Indexed email
            $table->string('password', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('cnic_front', 255);
            $table->string('cnic_back', 255);
            $table->string('location', 255)->nullable();
            $table->tinyInteger('repair_service')->default(0)->nullable();
            $table->string('otp', 255)->nullable();
			$table->string('otp_token', 255)->nullable();
			$table->string('type', 255)->nullable(); 
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_otp');
    }
}
