<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugToBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up(): void
{
    Schema::table('blogs', function (Blueprint $table) {
        $table->string('slug')->unique()->after('title'); 
    });
}

public function down(): void
{
    Schema::table('blogs', function (Blueprint $table) {
        $table->dropColumn('slug');
    });
}

}
