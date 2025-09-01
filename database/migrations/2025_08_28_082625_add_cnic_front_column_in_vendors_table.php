<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCnicFrontColumnInVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->tinyInteger('repair_service')->default(0)->after('longitude')->nullable();
            $table->string('cnic_front')->nullable()->after('repair_service');
            $table->string('cnic_back')->nullable()->after('cnic_front');
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
            $table->dropColumn('repair_service');
            $table->dropColumn('cnic_front');
            $table->dropColumn('cnic_back');
        });
    }
}
