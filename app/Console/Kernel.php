<?php

namespace App\Console;

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
        $schedule->command('poe:twitch update')->everyThirtyMinutes();

        // $schedule->command('poe:ladder')->hourly();

        $schedule->command('cache:clear')->dailyAt('13:00');
        $schedule->command('poe:tree')->dailyAt('13:00');
        // $schedule->command('poe:atlas')->dailyAt('13:10');
        $schedule->command('poe:update --leagues')->dailyAt('13:10');
        $schedule->command('poe:twitch update-token')->monthly();

        // $schedule->command('poe:ladder --update')->dailyAt('13:00');//needs laravel/horizon 

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
