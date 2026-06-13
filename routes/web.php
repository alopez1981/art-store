<?php

use App\Domain\Shipping\ShippingZoneRepository;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\ArtworkController as AdminArtworkController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\Artwork;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Catalog', [
        'artworks' => Artwork::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get(),
    ]);
})->name('catalog');

Route::get('/artworks/{artwork:slug}', function (Artwork $artwork, ShippingZoneRepository $shippingZones) {
    // Las piezas sin publicar no son visibles en la web pública.
    abort_unless($artwork->is_published, 404);

    return Inertia::render('ArtworkShow', [
        'artwork' => $artwork,
        'relatedArtworks' => Artwork::where('is_published', true)
            ->whereKeyNot($artwork->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get(),
        // Tarifas de envío desde Badalona, visibles antes de pagar.
        'shippingZones' => array_map(fn ($zone) => [
            'code' => $zone->code,
            'label' => $zone->label,
            'description' => $zone->description,
            'amount' => $zone->amount,
            'min_days' => $zone->minDays,
            'max_days' => $zone->maxDays,
        ], $shippingZones->all()),
    ]);
})->name('artworks.show');

Route::get('/checkout/success', [CheckoutController::class, 'success'])
    ->name('checkout.success');

Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])
    ->name('checkout.cancel');

// throttle: evita abuso del endpoint (cada llamada crea una sesión en Stripe).
Route::post('/checkout/{artwork}', [CheckoutController::class, 'createSession'])
    ->middleware('throttle:10,1')
    ->name('checkout.create');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])
        ->name('admin.login');
    // throttle: frena ataques de fuerza bruta sobre el login.
    Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('admin.login.store');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('/', fn () => redirect()->route('admin.artworks.index'));
    Route::resource('artworks', AdminArtworkController::class)->except('show');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
});
