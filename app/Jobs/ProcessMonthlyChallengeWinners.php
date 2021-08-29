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
use Morilog\Jalali\Jalalian;

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
        $month = Jalalian::now()->subMonths(1)->getMonth();
        $challenges = MonthlyChallenge::query()
            ->where('is_active', true)
            ->where('month', $month)
            ->where('ends_at', '<', $now)
            ->get();
        try {
            DB::beginTransaction();
            $closedChallenges = [];

            foreach ($challenges as $challenge) {
                $participants = $challenge->stats;
                foreach ($participants as $participant) {
                    $record = [
                        'monthly_challenge_id' => $challenge->id,
                        'user_id' => $participant->user_id,
                        'points' => $participant->amount,
                        'points_needed' => $challenge->goal_amount,
                        'prize_id' => $challenge->prize->id,
                        'has_won' => $participant->amount >= $challenge->goal_amount,
                    ];
                    MonthlyChallengeWinner::query()->create($record);
                }
                $challenge->is_active = false;
                $challenge->save();
                $closedChallenges[] = $challenge;
            }

            $currentMonth = Jalalian::now()->getMonth();
            $currentYear = Jalalian::now()->getYear();
            MonthlyChallenge::query()
                ->where('is_active', false)
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->update(['is_active' => true]);

            DB::commit();

            foreach ($closedChallenges as $challenge) {
                event(new ChallengeWasClosed($challenge));
            }

        } catch (\Exception $exception) {
            DB::rollBack();
        }

    }
}
