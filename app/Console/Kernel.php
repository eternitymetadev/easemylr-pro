<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\Report2ExportJob;

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
        $edate = config('export.end_date');
        $basec = config('export.base_client_id');
        $regc = config('export.reg_client_id');
        $branch = config('export.branch_id');
    
        $schedule->job(new Report2ExportJob($sdate, $edate, $basec, $regc, $branch))
        ->dailyAt('12:23')
        ->sendOutputTo(storage_path('logs/report2export.log'))
        ->emailOutputTo('vikas.singh@eternitysolutions.net');
      
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
