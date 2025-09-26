<?php


use App\Http\Controllers\EventController;
use App\Http\Controllers\AttendeeController;

Route::apiResource('events', EventController::class);

Route::get('events/{event}/attendees', [AttendeeController::class, 'index']);
Route::post('events/{event}/attendees', [AttendeeController::class, 'store']);
