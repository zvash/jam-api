<?php

namespace App\Jobs;

use App\Events\ChallengeWasClosed;
use App\Models\MonthlyChallenge;
use App\Models\MonthlyChallengeWinner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessMonthlyChallengeWinners implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $now = \Carbon\Carbon::now();
        $challenges = MonthlyChallenge::query()
            ->where('is_active', true)
            ->where('ends_at', '<', $now)
            ->get();
        foreach ($challenges as $challenge) {
            $winners = $challenge->stats()
                ->where('amount', '>=', $challenge->goal_amount)
                ->get();
            try {
                DB::beginTransaction();
                foreach ($winners as $winner) {
                    $record = [
                        'monthly_challenge_id' => $challenge->id,
                        'user_id' => $winner->user_id,
                        'points' => $winner->amount,
                        'points_needed' => $challenge->goal_amount,
                        'prize_id' => $challenge->prize->id,
                    ];
                    MonthlyChallengeWinner::query()->create($record);
                }
                $challenge->is_avtive = false;
                $challenge->save();
                DB::commit();
                event(new ChallengeWasClosed($challenge));
            } catch (\Exception $exception) {
                DB::rollBack();
            }
        }
    }
}
