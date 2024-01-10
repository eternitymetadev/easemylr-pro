<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\Report2ExportJob;

class MyExportCommand extends Command
{
    protected $signature = 'my:export';
    protected $description = 'Mis report Export';

    public function handle()
    {
        \Log::info('MyExportCommand started!');
        $sdate = config('export.start_date');
        $edate = config('export.end_date');
        
        Report2ExportJob::dispatch(
            $sdate,
            $edate,
            config('export.base_client_id'),
            config('export.reg_client_id'),
            config('export.branch_id')
        );

        \Log::info('MyExportCommand completed successfully!');

    }
}
