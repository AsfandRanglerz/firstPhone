<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorAndRepairingServiceToMobileListingsTable extends Migration
{
    public function up()
    {
        Schema::table('mobile_listings', function (Blueprint $table) {
            $table->string('color')->nullable()->after('model'); // yahan 'model' tumhare table ka actual column name hoga
            $table->boolean('repairing_service')->default(false)->after('color');
        });
    }

    public function down()
    {
        Schema::table('mobile_listings', function (Blueprint $table) {
            $table->dropColumn(['color', 'repairing_service']);
        });
    }
}
