<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderStatusLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->enum('status', [
                OrderStatus::PENDING,
                OrderStatus::REJECTED,
                OrderStatus::CANCELED_BY_CUSTOMER,
                OrderStatus::ACCEPTED_WAITING_CUSTOMER_APPROVAL,
                OrderStatus::ACCEPTED_WAITING_FOR_DRIVER,
                OrderStatus::ACCEPTED_BY_DRIVER,
                OrderStatus::DRIVER_HEADING_TO_LOCATION,
                OrderStatus::PICKED_UP,
                OrderStatus::ACCEPTED_DRIVER_NOT_NEEDED,
                OrderStatus::DELIVERED,
                OrderStatus::FINISHED,
            ]);
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
        Schema::dropIfExists('order_status_logs');
    }
}
