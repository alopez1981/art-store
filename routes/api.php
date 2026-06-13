<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', [CheckoutController::class, 'webhook'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->middleware('throttle:30,1')
    ->name('stripe.webhook');
