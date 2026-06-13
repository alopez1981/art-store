<script setup>
import {Head, Link, router} from '@inertiajs/vue3'

defineProps({
    orders: {
        type: Array,
        required: true,
    },
})

function formatAmount(amount) {
    if (amount === null || amount === undefined) return '—'
    return new Intl.NumberFormat('es-ES', {style: 'currency', currency: 'EUR'}).format(amount / 100)
}

function formatDate(iso) {
    if (!iso) return '—'
    return new Intl.DateTimeFormat('es-ES', {dateStyle: 'short', timeStyle: 'short'}).format(new Date(iso))
}

// La dirección llega como la entrega Stripe: {name, address: {line1, line2, city, postal_code, state, country}}
function formatAddress(shipping) {
    const a = shipping?.address
    if (!a) return null
    return [a.line1, a.line2, `${a.postal_code ?? ''} ${a.city ?? ''}`.trim(), a.state, a.country]
        .filter(Boolean)
        .join(', ')
}
</script>

<template>
    <Head title="Admin · Pedidos"/>

    <main class="min-h-screen bg-bone text-ink">
        <header class="border-b-2 border-ink bg-bone">
            <div class="mx-auto flex max-w-[1480px] items-center justify-between px-6 py-4 sm:px-10">
                <Link href="/" class="flex items-baseline gap-3">
                    <span class="headline text-3xl text-flag">MEL</span>
                    <span class="headline text-3xl text-ink">ATAEL</span>
                    <span class="eyebrow hidden text-ash-500 md:inline">/ ADMIN</span>
                </Link>
                <div class="flex items-center gap-6">
                    <Link href="/admin/artworks" class="eyebrow text-ink hover:text-flag">Obras</Link>
                    <Link href="/" class="eyebrow text-ink hover:text-flag">Catálogo</Link>
                    <button class="eyebrow text-ink hover:text-flag" @click="router.post('/admin/logout')">
                        Salir
                    </button>
                </div>
            </div>
        </header>

        <section class="mx-auto max-w-[1480px] px-6 py-12 sm:px-10">
            <div class="border-b-2 border-ink pb-8">
                <p class="eyebrow text-flag">Admin · Ventas</p>
                <h1 class="headline mt-4 text-5xl text-ink sm:text-6xl">Pedidos</h1>
                <p class="mt-3 max-w-md text-sm leading-6 text-ash-700">
                    Solo lectura. Los pedidos los registra el webhook de Stripe;
                    reembolsos y correcciones, desde el dashboard de Stripe.
                </p>
            </div>

            <div v-if="orders.length" class="mt-10 overflow-x-auto border-2 border-ink bg-bone">
                <table class="min-w-full">
                    <thead class="border-b-2 border-ink bg-bone-deep">
                        <tr class="text-left">
                            <th class="eyebrow px-5 py-4 text-ash-700">Obra</th>
                            <th class="eyebrow px-5 py-4 text-ash-700">Comprador</th>
                            <th class="eyebrow px-5 py-4 text-ash-700">Envío</th>
                            <th class="eyebrow px-5 py-4 text-ash-700">Total</th>
                            <th class="eyebrow px-5 py-4 text-ash-700">Estado</th>
                            <th class="eyebrow px-5 py-4 text-ash-700">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/20">
                        <tr v-for="order in orders" :key="order.id" class="align-top">
                            <td class="px-5 py-5">
                                <div class="flex items-start gap-4">
                                    <img
                                        v-if="order.artwork_image"
                                        :src="order.artwork_image"
                                        :alt="order.artwork_title"
                                        class="h-16 w-16 border-2 border-ink object-cover"
                                    />
                                    <p class="headline text-lg text-ink">{{ order.artwork_title }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-5 text-sm leading-6 text-ash-700">
                                <p class="font-bold text-ink">{{ order.customer_name || '—' }}</p>
                                <p>{{ order.customer_email || '—' }}</p>
                                <p>{{ order.customer_phone || '—' }}</p>
                            </td>
                            <td class="max-w-xs px-5 py-5 text-sm leading-6 text-ash-700">
                                <p v-if="order.shipping_zone_label" class="eyebrow text-ink">
                                    {{ order.shipping_zone_label }}
                                    <span v-if="order.shipping_amount !== null">
                                        · {{ formatAmount(order.shipping_amount) }}
                                    </span>
                                </p>
                                <p>{{ formatAddress(order.shipping_address) || 'Sin dirección' }}</p>
                            </td>
                            <td class="px-5 py-5 text-sm font-bold text-ink">
                                {{ formatAmount(order.amount_total) }}
                            </td>
                            <td class="px-5 py-5">
                                <span
                                    class="eyebrow inline-flex border-2 px-3 py-1"
                                    :class="
                                        order.payment_status === 'paid'
                                            ? 'border-ink bg-ink text-bone'
                                            : 'border-ash-300 bg-bone-deep text-ash-500'
                                    "
                                >
                                    {{ order.payment_status === 'paid' ? 'Pagado' : (order.payment_status || 'Pendiente') }}
                                </span>
                            </td>
                            <td class="px-5 py-5 text-sm text-ash-700">
                                {{ formatDate(order.paid_at || order.created_at) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="mt-10 border-2 border-ink bg-bone-deep px-8 py-14 text-center">
                <p class="eyebrow text-flag">Sin pedidos todavía</p>
                <p class="headline mt-4 text-3xl text-ink">El archivo espera su primera venta</p>
            </div>
        </section>
    </main>
</template>
