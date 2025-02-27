<?php

use App\Http\Controllers\FAQController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MeetingAttendeeController;
use App\Http\Controllers\MeetingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/faq', [FAQController::class, 'index'])->name('faq');
Route::post('/meeting', [MeetingController::class, 'store'])->name('meeting.store');
Route::get('/meeting/{meeting:identifier}', [MeetingController::class, 'show'])->name('meeting.show');
Route::post('/meeting/{meeting:identifier}/attendee/store', [MeetingAttendeeController::class, 'store'])->name('meeting.attendee.store');
Route::post('/meeting/attendee/{attendee:identifier}/destroy', [MeetingAttendeeController::class, 'destroy'])->name('meeting.attendee.destroy');
