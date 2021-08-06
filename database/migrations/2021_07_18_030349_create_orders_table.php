<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('driver_id')->nullable()->constrained('users');
            $table->foreignId('location_id')->constrained();
            $table->string('description')->nullable();
            $table->boolean('requires_driver')->default(false);
            $table->boolean('final_price_needed')->default(false);

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
            ])->default(OrderStatus::PENDING);

            $table->timestamp('pickup_date')->nullable();
            $table->double('approximate_weight')->nullable();
            $table->double('final_weight')->nullable();
            $table->unsignedInteger('approximate_price')->nullable();
            $table->unsignedInteger('final_price')->nullable();
            $table->unsignedInteger('final_driver_price')->nullable();
            $table->text('waybill_image')->nullable();
            $table->string('waybill_number')->nullable();
            $table->text('evacuation_permit_image')->nullable();
            $table->string('evacuation_permit_number')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
