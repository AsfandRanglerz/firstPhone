<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mobile_listings', function (Blueprint $table) {
            $table->integer('quantity')->default(0)->after('id'); 
            // "after('id')" optional hai, column position ke liye
        });
    }

    public function down(): void
    {
        Schema::table('mobile_listings', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
