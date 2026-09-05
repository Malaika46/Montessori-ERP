<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the public landing page.
     */
    public function index()
    {
        return view('landing');
    }
}
