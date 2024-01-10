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
        \Log::info('Report2ExportJob started processing...');
        $sdate = config('export.start_date');
        $schedule->call(function () {
            $sdate = config('export.start_date');
            $edate = config('export.end_date');
    
            Report2ExportJob::dispatch(
                $sdate,
                $edate,
                config('export.base_client_id'),
                config('export.reg_client_id'),
                config('export.branch_id')
            );
        })->dailyAt('10:43');
       \Log::info($sdate);
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
