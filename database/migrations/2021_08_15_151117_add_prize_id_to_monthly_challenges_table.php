<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrizeIdToMonthlyChallengesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monthly_challenges', function (Blueprint $table) {
            $table->dropColumn('prize');
            $table->foreignId('prize_id')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monthly_challenges', function (Blueprint $table) {
            $table->dropColumn('prize_id');
            $table->string('prize')->nullable()->after('description');
        });
    }
}
