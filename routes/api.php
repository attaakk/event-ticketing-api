<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketTypeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//  Auth
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/users/me', [AuthController::class, 'me']);

// Event Discovery
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{eventId}', [EventController::class, 'show']);

// Organizer
Route::middleware('auth:sanctum')->group(function () {
Route::post('/events/store', [EventController::class, 'store']);
Route::put('/events/{eventId}/update', [EventController::class, 'update']);
Route::post('/events/{eventId}/ticket-types/store', [TicketTypeController::class, 'store']);
Route::put('/events/{eventId}/ticket-types/{typeId}/update', [TicketTypeController::class, 'update']);
Route::delete('/events/{eventId}/ticket-types/{typeId}/delete', [TicketTypeController::class, 'destroy']);
// Attendee
Route::post('/orders/store', [OrderController::class, 'store']);
Route::get('/my-orders', [OrderController::class, 'myOrders']);
Route::get('/my-tickets', [OrderController::class, 'myTickets']);
// Admin
Route::post('/admin/users/store', [AdminController::class, 'store']);
Route::get('/admin/events', [AdminController::class, 'events']);
Route::delete('/admin/events/{eventId}/delete', [AdminController::class, 'destroyEvent']);
Route::delete('/events/{eventId}/delete', [EventController::class, 'destroy']);
});
