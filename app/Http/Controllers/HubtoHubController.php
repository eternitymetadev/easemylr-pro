<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Vendor;
use Illuminate\Http\Request;

class HubtoHubController extends Controller
{
    public $prefix;
    public $title;
    public $segment;

    public function __construct()
    {
        $this->title = "Secondary Reports";
        $this->segment = \Request::segment(2);
        $this->req_link = \Config::get('req_api_link.req');
    }

   public function hubtransportation()
   {
       $this->prefix = request()->route()->getPrefix();

       return view('hub-transportation.hub-transportation', ['prefix' => $this->prefix]);
   }
}
