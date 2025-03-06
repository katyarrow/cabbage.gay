<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AdminKillSwitchController extends Controller
{
    public function store(Request $request) {
        $request->validate(['password' => 'required|max:255']);
        if(!Hash::check($request->password, $request->user()->password)) {
            return back()->with('password','Invalid password');
        }
        Artisan::call('down');
        Auth::logout();
        session()->flush();
        return redirect()->back();
    }
}
