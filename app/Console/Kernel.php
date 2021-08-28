<?php

namespace App\Console;

use App\Jobs\ProcessDriverAcceptRequests;
use App\Jobs\ProcessMonthlyChallengeWinners;
use App\Jobs\UpdatePriceList;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new ProcessDriverAcceptRequests())->everyMinute()
            ->withoutOverlapping(5);

        $schedule->job(new ProcessMonthlyChallengeWinners())->dailyAt('00:05');

        $schedule->job(new UpdatePriceList())->everyMinute()
            ->withoutOverlapping(5);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
