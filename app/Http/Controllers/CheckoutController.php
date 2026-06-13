<?php

namespace App\Http\Controllers;

use App\Domain\Shipping\ShippingZoneRepository;
use App\Models\Artwork;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class CheckoutController extends Controller
{
    public function __construct(private readonly ShippingZoneRepository $shippingZones)
    {
    }

    public function createSession(Request $request, Artwork $artwork)
    {
        abort_if($artwork->isSold(), 410, 'Esta obra ya ha sido vendida.');
        abort_unless($artwork->is_published, 404);

        $validated = $request->validate([
            'shipping_zone' => [
                'required',
                'string',
                Rule::in(array_map(fn ($z) => $z->code, $this->shippingZones->all())),
            ],
        ]);

        $zone = $this->shippingZones->findByCode($validated['shipping_zone']);

        Stripe::setApiKey(config('services.stripe.secret'));

        $imageUrl = Str::startsWith($artwork->image_url, ['http://', 'https://'])
            ? $artwork->image_url
            : url($artwork->image_url);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $artwork->price,
                    'product_data' => array_filter([
                        'name' => $artwork->title,
                        'description' => $artwork->description ?: null,
                        'images' => [$imageUrl],
                    ]),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            // Datos del comprador: dirección de envío + teléfono además del email.
            'shipping_address_collection' => [
                'allowed_countries' => $zone->countries,
            ],
            'phone_number_collection' => ['enabled' => true],
            'shipping_options' => [[
                'shipping_rate_data' => [
                    'type' => 'fixed_amount',
                    'display_name' => sprintf('Envío desde Badalona · %s', $zone->label),
                    'fixed_amount' => [
                        'amount' => $zone->amount,
                        'currency' => 'eur',
                    ],
                    'delivery_estimate' => [
                        'minimum' => ['unit' => 'business_day', 'value' => $zone->minDays],
                        'maximum' => ['unit' => 'business_day', 'value' => $zone->maxDays],
                    ],
                ],
            ]],
            // Pieza única: la sesión caduca en 30 min (mínimo de Stripe)
            // para reducir la ventana de doble venta.
            'expires_at' => now()->addMinutes(30)->getTimestamp(),
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'metadata' => [
                'artwork_id' => $artwork->id,
                'shipping_zone' => $zone->code,
            ],
        ]);

        return response()->json(['url' => $session->url]);
    }

    public function success(Request $request)
    {
        $order = Order::with('artwork')
            ->where('stripe_session_id', $request->string('session_id'))
            ->first();

        return Inertia::render('CheckoutSuccess', [
            'order' => $order ? [
                'id' => $order->id,
                'stripe_session_id' => $order->stripe_session_id,
                'stripe_customer_email' => $order->stripe_customer_email,
                'amount_total' => $order->amount_total,
                'currency' => $order->currency,
                'shipping_amount' => $order->shipping_amount,
                'shipping_address' => $order->shipping_address,
                'paid_at' => optional($order->paid_at)?->toIso8601String(),
                'artwork' => $order->artwork ? [
                    'id' => $order->artwork->id,
                    'title' => $order->artwork->title,
                    'description' => $order->artwork->description,
                    'image_url' => $order->artwork->image_url,
                ] : null,
            ] : null,
        ]);
    }

    public function cancel()
    {
        return Inertia::render('CheckoutCancel');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $artworkId = (int) data_get($session, 'metadata.artwork_id');

            // Stripe (API actual) entrega la dirección de envío en
            // collected_information.shipping_details; mantenemos el
            // fallback legacy shipping_details por compatibilidad.
            $shippingDetails = data_get($session, 'collected_information.shipping_details')
                ?? data_get($session, 'shipping_details');

            DB::transaction(function () use ($artworkId, $session, $shippingDetails) {
                Order::updateOrCreate(
                    ['stripe_session_id' => $session->id],
                    [
                        'artwork_id' => $artworkId,
                        'stripe_payment_intent_id' => $session->payment_intent,
                        'stripe_customer_email' => $session->customer_details->email ?? null,
                        'customer_name' => data_get($session, 'customer_details.name'),
                        'customer_phone' => data_get($session, 'customer_details.phone'),
                        'amount_total' => $session->amount_total,
                        'currency' => strtoupper((string) $session->currency),
                        'payment_status' => $session->payment_status,
                        'shipping_zone' => data_get($session, 'metadata.shipping_zone'),
                        'shipping_amount' => data_get($session, 'shipping_cost.amount_total'),
                        'shipping_address' => $shippingDetails
                            ? json_decode(json_encode($shippingDetails), true)
                            : null,
                        'paid_at' => $session->payment_status === 'paid' ? now() : null,
                        'payload' => $session->toArray(),
                    ]
                );

                Artwork::where('id', $artworkId)
                    ->whereNull('vendido_at')
                    ->update(['vendido_at' => now()]);
            });
        }

        return response('OK', 200);
    }
}
