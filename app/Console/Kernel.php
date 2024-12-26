<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        //$schedule->command('app:monthly-store-task')->everyMinute();
        $schedule->command('app:daily-store-task')
             ->daily()
             ->at('01:00');
        $schedule->command('app:daily1201-task')
             ->daily()
            //  ->at('00:01');
             ->at('22:01'); /** changed to every 10:01 PM (UCT) equivalent to 5:01 PM (CST) */
        $schedule->command('app:daily-job-update-store')
             ->daily()
             ->at('00:01');
        // $schedule->command('app:weekly-store-task')
        //     ->weekly()->mondays()
        //     ->at('00:15');
        $schedule->command('app:monthly-store-task')->monthlyOn(1, '00:00');
        // $schedule->command('app:send-s-m-s-every-half-hour')->cron('30 * * * *');
        $schedule->command('app:hourly30-o-clock-command')->cron('30 * * * *');
        // $schedule->command('app:daily1700-task')
        //      ->daily()
        //      ->at('17:00');
        $schedule->command('app:daily0800-task')
             ->daily()
             ->at('08:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
