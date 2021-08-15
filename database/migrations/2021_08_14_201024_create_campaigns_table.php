<?php

use App\Enums\GoalType;
use App\Enums\UserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->enum('user_type', [
                UserType::SELLER,
                UserType::DRIVER,
            ]);
            $table->enum('goal_type', [
                GoalType::ORDER_COUNT,
                GoalType::WEIGHT,
            ]);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        \App\Models\Campaign::createIfNotExists('کمپین دائمی فروشنگان', UserType::SELLER, GoalType::WEIGHT);
        \App\Models\Campaign::createIfNotExists('کمپین دائمی رانندگان', UserType::DRIVER, GoalType::ORDER_COUNT);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}
