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
        Commands\ActivitiesPayout::class,
        Commands\GymPayout::class,
        Commands\PassExpire::class,
        Commands\SendBeforeExpireNotification::class,
        Commands\SendNotifications::class
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
        // $schedule->command('inspire')->hourly();
        $schedule->command('send:notifications')->everyMinute();
        $schedule->command('activity:payout')
        ->daily()/*weekly()->mondays()->at('01:00')*/;
        $schedule->command('gym:payout')
        ->daily()/*weekly()->mondays()->at('01:00')*/;
        $schedule->command('pass:expire')
        ->dailyAt('00:02');/*weekly()->mondays()->at('01:00')*/
        $schedule->command('send:before:expire:notification')
        ->dailyAt('00:02');
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
