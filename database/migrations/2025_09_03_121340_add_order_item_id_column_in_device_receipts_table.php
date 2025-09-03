<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderItemIdColumnInDeviceReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_receipts', function (Blueprint $table) {
            $table->unsignedBigInteger('order_item_id')->nullable()->after('order_id');
            $table->string('payment_id')->nullable()->after('product_id');
            

            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_receipts', function (Blueprint $table) {
            $table->dropForeign(['order_item_id']);
            $table->dropColumn('order_item_id');
            $table->dropColumn('payment_id');
        });
    }
}
