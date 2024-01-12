<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Exports\Report2Export;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\ReportExportNotification;
use Illuminate\Support\Facades\Notification;

class Report2ExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $exportParams;
    protected $baseclient_id;
    protected $regclient_id;
    protected $branch_id;

    public function __construct($exportParams,$baseclient_id, $regclient_id, $branch_id)
    {
        $this->exportParams = $exportParams;
        $this->baseclient_id = $baseclient_id;
        $this->regclient_id = $regclient_id;
        $this->branch_id = $branch_id;
    }

    public function handle()
    {
        \Log::info('Report2ExportJob started processing...');

        // Generate paths for the three reports
        $paths = [
            storage_path('app/public/mis/mis1.xlsx'),
            storage_path('app/public/mis/mis2.xlsx'),
            storage_path('app/public/mis/mis3.xlsx'),
        ];

        // Generate exports for each report
        foreach ($this->exportParams as $index => $exportParams) {
            $export = new Report2Export($exportParams[0],$exportParams[1], $this->baseclient_id, $this->regclient_id, $this->branch_id);
            Excel::store($export, $paths[$index]);
        }

        // Get an array of email addresses from the environment variable
        $emailAddresses = explode(',', env('MIS_EMAILS'));

        // Send email notification to each email address with all report attachments
        foreach ($emailAddresses as $emailAddress) {
            Notification::route('mail', $emailAddress)
                ->notify(new ReportExportNotification($reports));
        }

        \Log::info('Report2ExportJob processed: ' . implode(', ', $paths));
    }
    
    
}

