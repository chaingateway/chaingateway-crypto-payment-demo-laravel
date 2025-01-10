<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/payment', [PaymentController::class, 'showPaymentPage']);
Route::post('/start-payment-session', [PaymentController::class, 'startPaymentSession']);
Route::get('/payment-session/{id}', [PaymentController::class, 'showPaymentSession'])->name('showPaymentSession');
Route::post('/webhook', [PaymentController::class, 'handleWebhook'])->name('handleWebhook');
