<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RegionalClient;

class RegionalReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regional:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("eertergeg");
        $regional_details = RegionalClient::all();
        foreach($regional_details as $regional){
            \Log::info($regional);
        }
      
    }
}
