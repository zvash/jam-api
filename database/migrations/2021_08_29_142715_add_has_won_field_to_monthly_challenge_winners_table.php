<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasWonFieldToMonthlyChallengeWinnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monthly_challenge_winners', function (Blueprint $table) {
            $table->boolean('has_won')->default(true)->after('prize_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monthly_challenge_winners', function (Blueprint $table) {
            $table->dropColumn('has_won');
        });
    }
}
