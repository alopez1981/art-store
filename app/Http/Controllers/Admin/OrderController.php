<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Shipping\ShippingZoneRepository;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Inertia\Inertia;

/**
 * Solo lectura: los pedidos los crea exclusivamente el webhook
 * de Stripe. Cualquier corrección (reembolsos, etc.) se hace
 * en el dashboard de Stripe, nunca aquí.
 */
class OrderController extends Controller
{
    public function __construct(private readonly ShippingZoneRepository $shippingZones)
    {
    }

    public function index()
    {
        return Inertia::render('Admin/Orders/Index', [
            'orders' => Order::with('artwork')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn (Order $order) => [
                    'id' => $order->id,
                    'artwork_title' => $order->artwork?->title ?? '—',
                    'artwork_image' => $order->artwork?->image_url,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->stripe_customer_email,
                    'customer_phone' => $order->customer_phone,
                    'amount_total' => $order->amount_total,
                    'shipping_amount' => $order->shipping_amount,
                    'shipping_zone_label' => $order->shipping_zone
                        ? ($this->shippingZones->findByCode($order->shipping_zone)?->label ?? $order->shipping_zone)
                        : null,
                    'shipping_address' => $order->shipping_address,
                    'payment_status' => $order->payment_status,
                    'paid_at' => optional($order->paid_at)?->toIso8601String(),
                    'created_at' => $order->created_at->toIso8601String(),
                ]),
        ]);
    }
}
