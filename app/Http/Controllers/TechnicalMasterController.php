<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TechnicalMasterController extends Controller
{
    public $prefix;
    public $title;
    public $segment;

    public function __construct()
    {
        $this->title = "Secondary Reports";
        $this->segment = \Request::segment(2);

    }
    public function techicalMaster()
    {
        $this->prefix = request()->route()->getPrefix();

        return view('technical-master.technical-masters', ['prefix' => $this->prefix]);
    }
}
