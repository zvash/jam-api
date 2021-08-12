<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyChallengesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_challenges', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type', [
                \App\Enums\UserType::SELLER,
                \App\Enums\UserType::DRIVER,
            ]);
            $table->unsignedInteger('year');
            $table->unsignedInteger('month');
            $table->text('description');
            $table->text('prize');
            $table->enum('goal_type', [
                \App\Enums\GoalType::ORDER_COUNT,
                \App\Enums\GoalType::WEIGHT,
            ]);
            $table->double('goal_amount');
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->unique(['user_type', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monthly_challenges');
    }
}
