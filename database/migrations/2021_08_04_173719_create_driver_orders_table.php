<?php

use App\Enums\DriverOrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', [
                DriverOrderStatus::SUGGESTED,
                DriverOrderStatus::CANCELED_BY_DRIVER,
                DriverOrderStatus::ACCEPTED_BY_DRIVER,
                DriverOrderStatus::ASSIGNED_TO_DRIVER,
                DriverOrderStatus::ORDER_REJECTED,
                DriverOrderStatus::EXPIRED,
                DriverOrderStatus::TAKEN,
            ])->index();
            $table->string('order_token')->index();
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
        Schema::dropIfExists('driver_orders');
    }
}
