<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;

class AdminLogoutController extends Controller
{
    public function destroy()
    {
        Auth::logout();
        session()->flush();

        return redirect()->route('login');
    }
}
