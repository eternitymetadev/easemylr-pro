<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function create()
    {
        $this->prefix = request()->route()->getPrefix();
        return view('vendors.create-vendor',['prefix' => $this->prefix]);
    }
}
