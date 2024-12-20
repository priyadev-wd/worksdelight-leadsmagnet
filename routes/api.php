<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleOAuthController;
use App\Http\Controllers\TriggerAppointmentController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('google/calendar/events/webhook', [GoogleOAuthController::class, 'handleWebhook'])->name('calendar.events.handle.webhook');
Route::post('triggerAppointmentUpdate', [TriggerAppointmentController::class, 'triggerAppointmentUpdate'])->name('triggerAppointmentUpdate');
