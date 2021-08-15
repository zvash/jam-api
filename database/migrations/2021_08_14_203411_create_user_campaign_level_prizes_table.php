<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCampaignLevelPrizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_campaign_level_prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('campaign_level_id');
            $table->foreignId('prize_id');
            $table->double('milestone');
            $table->boolean('handed_over')->default(false);
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
        Schema::dropIfExists('user_campaign_level_prizes');
    }
}
