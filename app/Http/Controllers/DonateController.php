<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DonateController extends Controller
{
    public function index(): View
    {
        return view('donate');
    }
}
