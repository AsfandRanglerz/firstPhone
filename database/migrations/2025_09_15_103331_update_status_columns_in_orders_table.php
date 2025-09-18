<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateStatusColumnsInOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('inprogress','shipped','delivered','cancelled') NOT NULL DEFAULT 'inprogress'");

            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('paid','unpaid') NOT NULL DEFAULT 'unpaid'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('inprogress','shipped','delivered','cancelled') NOT NULL DEFAULT 'inprogress'");

            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('paid','unpaid') NOT NULL DEFAULT 'unpaid'");
        });
    }
}
