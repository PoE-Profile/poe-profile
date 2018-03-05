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
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('poe:twitch update')->everyTenMinutes();

        $schedule->command('poe:ladder')->hourly();
        $schedule->command('poe:ladder --update')->dailyAt('13:00');

        // $schedule->command('poe:ladder --league=\'Hardcore Abyss\' --total=2000')->hourly();
        // $schedule->command('poe:ladder --league=\'Hardcore Abyss\' --total=2000 --update')->dailyAt('13:40');
        //
        // $schedule->command('poe:ladder --league=\'SSF Abyss\' --total=2000')->hourly();
        // $schedule->command('poe:ladder --league=\'SSF Abyss\' --total=2000 --update')->dailyAt('14:20');
        //
        // $schedule->command('poe:ladder --league=\'SSF Hardcore Abyss\' --total=2000')->hourly();
        // $schedule->command('poe:ladder --league=\'SSF Hardcore Abyss\' --total=2000 --update')->dailyAt('15:00');


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
