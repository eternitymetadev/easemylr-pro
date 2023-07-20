<?php

// app/Console/Commands/CheckPaidStatusCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ReportController;

class CheckPaidStatusCommand extends Command
{
    protected $signature = 'check:paid_status';
    
    protected $description = 'Check paid status for payment requests and PRS payment requests.';

    public function handle()
    {

                // // Create an instance of the ApiController
                // $VendorController = new VendorController();
                // // Call the `callApi` method
                // $VendorController->check_paid_status();

                $ReportController = new ReportController();
                $ReportController->regionalReport();

    }
}
