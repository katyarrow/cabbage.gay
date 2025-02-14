<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MeetingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/meeting', [MeetingController::class,'show'])->name('meeting.show');
