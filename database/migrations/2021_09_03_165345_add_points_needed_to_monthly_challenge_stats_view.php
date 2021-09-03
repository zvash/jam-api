<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPointsNeededToMonthlyChallengeStatsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE VIEW monthly_challenge_stats 
            AS
            SELECT
                mc.id as monthly_challenge_id,
                mc.prize_id as prize_id,
                o.user_id as user_id,
                mc.goal_amount as goal_amount,
                SUM(o.final_weight) as 'amount'
            FROM
                monthly_challenges mc,
                orders o
            WHERE
                mc.is_active = true AND
                mc.user_type = 'seller' AND
                o.status = 'finished' AND
                o.finished_at IS NOT NULL AND
                o.finished_at <= mc.ends_at AND 
                o.finished_at >= mc.starts_at AND
                o.final_weight IS NOT NULL
            GROUP BY mc.id, o.user_id
            UNION
            SELECT
                mc.id as monthly_challenge_id,
                mc.prize_id as prize_id,
                o.driver_id as user_id,
                mc.goal_amount as goal_amount,
                COUNT(o.id) as 'amount'
            FROM
                monthly_challenges mc,
                orders o
            WHERE
                mc.is_active = true AND
                mc.user_type = 'driver' AND
                o.driver_id IS NOT NULL AND
                o.status = 'finished' AND
                o.finished_at IS NOT NULL AND
                o.finished_at <= mc.ends_at AND 
                o.finished_at >= mc.starts_at
            GROUP BY mc.id, o.driver_id;
                
        ");
    }
}
