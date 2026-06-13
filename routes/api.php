<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', [CheckoutController::class, 'webhook'])
    ->name('stripe.webhook');
