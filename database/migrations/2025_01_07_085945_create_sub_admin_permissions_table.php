<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubAdminPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_admin_id')->constrained()->cascadeOnDelete();
            $table->foreignId('side_menu_id')->constrained('side_menus')->cascadeOnDelete();
            $table->string('permissions')->nullable();
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
        Schema::dropIfExists('sub_admin_permissions');
    }
}
