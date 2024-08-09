<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function switch($currentMode)
    {   
        session()->put('currentMode',$currentMode);
        return back();
    }
}