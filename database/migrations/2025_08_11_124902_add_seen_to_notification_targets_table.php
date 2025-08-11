<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeenToNotificationTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_targets', function (Blueprint $table) {
            $table->boolean('seen')->default(false)->after('targetable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_targets', function (Blueprint $table) {
            $table->dropColumn('seen');
        });
    }
}
