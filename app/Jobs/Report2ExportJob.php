<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Exports\Report2Export;
use Maatwebsite\Excel\Facades\Excel;

class Report2ExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $startdate;
    protected $enddate;
    protected $baseclient_id;
    protected $regclient_id;
    protected $branch_id;

    public function __construct($startdate, $enddate, $baseclient_id, $regclient_id, $branch_id)
    {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->baseclient_id = $baseclient_id;
        $this->regclient_id = $regclient_id;
        $this->branch_id = $branch_id;
    }

    public function handle()
    {
        \Log::info('Report2ExportJob started processing...');

    
        $export = new Report2Export($this->startdate, $this->enddate, $this->baseclient_id, $this->regclient_id, $this->branch_id);
    
        $path = storage_path('app/public/mis/mis2.xlsx');
        Excel::store($export, $path);
    
        \Log::info('Report2ExportJob processed: ' . $path);

    }
    
}

