<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sub_admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_admin_id')->index();
            $table->unsignedBigInteger('side_menu_id')->index();
            $table->string('permissions')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('sub_admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('side_menu_id')->references('id')->on('side_menus')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_admin_permissions');
    }
};