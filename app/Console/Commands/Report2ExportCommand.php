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
        $sdate = config('export.start_date');
        $edate = config('export.end_date');

        \Log::info('Dispatching Report2ExportJob...');

        Report2ExportJob::dispatch(
            $sdate,
            $edate,
            config('export.base_client_id'),
            config('export.reg_client_id'),
            config('export.branch_id')
        );

        \Log::info('Report2ExportJob dispatched successfully.');
    }
}
