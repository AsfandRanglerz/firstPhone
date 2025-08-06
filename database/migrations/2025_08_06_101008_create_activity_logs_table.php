<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('performed_by_sub_admin_id')->nullable()->index();
            $table->unsignedBigInteger('target_sub_admin_id')->nullable()->index();
            $table->string('action');
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Foreign Keys (agar tables mojood hain to uncomment karna)
            // $table->foreign('performed_by_sub_admin_id')->references('id')->on('sub_admins')->onDelete('set null');
            // $table->foreign('target_sub_admin_id')->references('id')->on('sub_admins')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};