<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController
{
    public function show(): View
    {
        return view("welcome");
    }    
}