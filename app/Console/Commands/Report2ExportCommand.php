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
        $sdate = now()->subDays(15)->toDateString();
        $edate = now()->toDateString();
        $baseclient_id = NULL;
        $regclient_id = NULL;
        $branch_id = NULL;

        \Log::info('Dispatching Report2ExportJob...' .$sdate .' to '. $edate);

        Report2ExportJob::dispatch(
            $sdate,
            $edate,
            $baseclient_id,
            $regclient_id,
            $branch_id
        );

        \Log::info('Report2ExportJob dispatched successfully.');
    }
}
