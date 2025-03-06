<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Models\User;
use App\Notifications\AdminLoginNotification;
use Auth;
use Hash;
use Illuminate\Support\Facades\Notification;

class AdminLoginController extends Controller
{
    public function index() {
        return view("admin.login");
    }

    public function store(AdminLoginRequest $request) {
        $user = User::where('username', $request->username)->first();
        if(!$user) {
            return redirect()->back()->withErrors(['username' => 'Invalid login']);
        }
        if(!Hash::check($request->password, $user->password)) {
            return redirect()->back()->withErrors(['username' => 'Invalid login']);
        }
        Notification::route('mail', env('ADMIN_NOTIFICATION_EMAIL'))
            ->notify(new AdminLoginNotification($user));
        session()->flush();
        Auth::login($user);
        return redirect()->route('admin.index');
    }
}
