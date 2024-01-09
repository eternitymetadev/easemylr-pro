<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected $commands = [
        Commands\RegionalReport::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('regional:report')
        //          ->everyMinute();
        $schedule->call(function () {
        $sdate = '2023-11-01';
        $edate = '2024-01-05';
        Report2ExportJob::dispatch(
            $sdate,
            $edate,
            $request->baseclient_id,
            $request->regclient_id,
            $request->branch_id
        );
       })->daily(); 
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
