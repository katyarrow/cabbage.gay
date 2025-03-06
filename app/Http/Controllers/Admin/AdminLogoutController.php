<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;

class AdminLogoutController extends Controller
{
    public function destroy() {
        Auth::logout();
        session()->flush();
        return redirect()->route('login');
    }
}
