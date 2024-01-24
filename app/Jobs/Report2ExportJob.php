<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Exports\Report2JobExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\ReportExportNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

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

        $paths = [];
    
        // Generate exports for each report
        foreach ($this->exportParams as $index => $exportParams) {
            $fromDate = $exportParams[0];
            $toDate = $exportParams[1];
    
            $filename = 'mis_' . Carbon::parse($fromDate)->format('M_Y') . '.xlsx';
    
            // Replace forward slashes with backslashes in the filename
            $filename = str_replace('/', '\\', $filename);
    
            // Generate the storage path for the report
            $path = storage_path('app/public/mis/' . $filename);
    
            // Add the path to the array
            $paths[] = $path;
            $export = new Report2JobExport($exportParams[0], $exportParams[1], $this->baseclient_id, $this->regclient_id, $this->branch_id);
            Excel::store($export, $paths[$index]);
        }
    

        // Get an array of email addresses from the environment variable
        $emailAddresses = explode(',', env('MIS_EMAILS'));
        // Extract the first email address as the primary recipient (To)
        $toEmail = array_shift($emailAddresses);

        // Send email notification to the primary recipient (To) and add BCC recipients
        Notification::to($toEmail)
            ->bcc($emailAddresses)
            ->notify(new ReportExportNotification($emailAddresses));

        \Log::info('Report2ExportJob processed: ' . implode(', ', $paths));
    }
    
    
}

