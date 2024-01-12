<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\Report2ExportJob;

class Report2ExportCommand extends Command
{
    protected $signature = 'report2export:run';
    protected $description = 'Run the report export process';

    public function handle()
    {
        $sdate = now()->firstOfMonth()->toDateString();
        $edate = now()->toDateString();
    
        $lastMonth = now()->subMonth();
        $last_month_start_date = $lastMonth->firstOfMonth()->toDateString();
        $last_month_end_date = $lastMonth->lastOfMonth()->toDateString();
    
        $thirdLastMonth = now()->subMonths(2);
        $third_last_month_start_date = $thirdLastMonth->firstOfMonth()->toDateString();
        $third_last_month_end_date = $thirdLastMonth->lastOfMonth()->toDateString();
    
        $baseclient_id = NULL;
        $regclient_id = NULL;
        $branch_id = NULL;
    
        $exportParams = [
            [$sdate, $edate],
            [$last_month_start_date, $last_month_end_date],
            [$third_last_month_start_date, $third_last_month_end_date],
        ];
    
        \Log::info('Dispatching Report2ExportJob...' . $sdate . ' to ' . $edate);
    
        Report2ExportJob::dispatch(
            $exportParams,
            $baseclient_id,
            $regclient_id,
            $branch_id
        );
    
        \Log::info('Report2ExportJob dispatched successfully.');
    }
}
