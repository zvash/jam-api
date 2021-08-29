<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHandedOverFieldToMonthlyChallengeWinnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monthly_challenge_winners', function (Blueprint $table) {
            $table->boolean('handed_over')->default(false)->after('has_won');
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
            $table->dropColumn('handed_over');
        });
    }
}
