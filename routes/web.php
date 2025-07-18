<?php

use App\Http\Controllers\Admin\AdminKillSwitchController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminLogoutController;
use App\Http\Controllers\Admin\AdminPanelController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\DonateController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MeetingAttendeeController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\RefreshCsrfTokenController;
use App\Http\Controllers\ReleasesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/faq', [FAQController::class, 'index'])->name('faq');
Route::get('/releases', [ReleasesController::class, 'index'])->name('releases');
if (config('donate.active')) {
    Route::get('/donate', [DonateController::class, 'index'])->name('donate');
}
Route::post('/meeting', [MeetingController::class, 'store'])->name('meeting.store');
Route::get('/meeting/{meeting:identifier}', [MeetingController::class, 'show'])->name('meeting.show');
Route::post('/meeting/{meeting:identifier}/attendee/store', [MeetingAttendeeController::class, 'store'])->name('meeting.attendee.store');
Route::post('/meeting/attendee/{attendee:identifier}/destroy', [MeetingAttendeeController::class, 'destroy'])->name('meeting.attendee.destroy');
Route::get('/captcha/get-challenge', [CaptchaController::class, 'index'])->name('captcha.index');
Route::post('/captcha/verify/{captcha}', [CaptchaController::class, 'verify'])->name('captcha.verify');
Route::get('/refresh-csrf-token', [RefreshCsrfTokenController::class, 'store'])->name('refresh-csrf-token');

Route::middleware(['guest'])->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'index'])->name('login');
    Route::post('/admin/login', [AdminLoginController::class, 'store'])->name('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminPanelController::class, 'index'])->name('admin.index');
    Route::post('/admin/killswitch', [AdminKillSwitchController::class, 'store'])->name('admin.killswitch');
    Route::delete('/logout', [AdminLogoutController::class, 'destroy'])->name('logout');
});
