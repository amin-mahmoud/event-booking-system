<?php

use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\TicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('events', EventController::class);

    Route::post('/events/{event}/tickets', [TicketController::class, 'store']);
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])
        ->middleware('role:admin,organizer');

    // Booking routes
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/tickets/{ticket}/bookings', [BookingController::class, 'store'])->middleware('prevent.double.booking');
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);


    // Payment routes
    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'store']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);


    Route::get('/notifications', function () {


        return response()->json([auth()->user()->notifications,
            'message' => 'Notifications retrieved'], 200);

    });

    Route::post('/notifications/{id}/read', function ($id) {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
        return response()->json(['message' => 'Notification marked as read']);
    });

});

Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);
