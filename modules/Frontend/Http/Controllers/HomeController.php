<?php

namespace Juzaweb\Frontend\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('frontend::home');
    }
}
