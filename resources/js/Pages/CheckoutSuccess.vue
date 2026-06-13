<script setup>
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    order: {
        type: Object,
        default: null,
    },
})

function formatAmount(amount, currency = 'EUR') {
    if (!amount) return null
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency }).format(amount / 100)
}

function formatDate(value) {
    if (!value) return null
    return new Intl.DateTimeFormat('es-ES', {
        dateStyle: 'long',
        timeStyle: 'short',
    }).format(new Date(value))
}
</script>

<template>
    <Head title="Compra confirmada" />

    <main class="min-h-screen bg-bone text-ink">
        <header class="border-b-2 border-ink bg-bone">
            <div class="mx-auto flex max-w-[1480px] items-center justify-between px-6 py-4 sm:px-10">
                <Link href="/" class="flex items-baseline gap-3">
                    <span class="headline text-3xl text-flag">MEL</span>
                    <span class="headline text-3xl text-ink">ATAEL</span>
                </Link>
                <Link href="/" class="eyebrow text-ink hover:text-flag">← Catálogo</Link>
            </div>
        </header>

        <section class="mx-auto max-w-4xl px-6 py-16 sm:px-10">
            <div class="border-b-2 border-ink pb-8">
                <p class="eyebrow text-flag">Pago confirmado</p>
                <h1 class="headline mt-4 text-5xl text-ink sm:text-6xl lg:text-7xl">
                    Compra registrada en archivo
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-ash-700">
                    Stripe ha aceptado el pago. La obra queda reservada y registrada
                    como vendida en el archivo público.
                </p>
            </div>

            <div v-if="order" class="mt-10 grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                <article class="border-2 border-ink bg-bone">
                    <img
                        v-if="order.artwork?.image_url"
                        :src="order.artwork.image_url"
                        :alt="order.artwork?.title ?? 'Obra adquirida'"
                        class="aspect-[4/5] w-full object-cover"
                    />
                    <div class="border-t-2 border-ink p-6">
                        <p class="eyebrow text-flag">Pieza adquirida</p>
                        <h2 class="headline mt-3 text-3xl text-ink">
                            {{ order.artwork?.title ?? 'Obra' }}
                        </h2>
                        <p v-if="order.artwork?.description" class="mt-3 text-sm leading-6 text-ash-700">
                            {{ order.artwork.description }}
                        </p>
                    </div>
                </article>

                <aside class="border-2 border-ink bg-ink p-8 text-bone">
                    <p class="eyebrow text-bone/60">Comprobante</p>
                    <dl class="mt-6 divide-y divide-bone/15">
                        <div class="flex items-baseline justify-between gap-4 py-4">
                            <dt class="eyebrow text-bone/60">Importe</dt>
                            <dd class="headline text-2xl text-flag">
                                {{ formatAmount(order.amount_total, order.currency) }}
                            </dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4 py-4">
                            <dt class="eyebrow text-bone/60">Email</dt>
                            <dd class="text-sm font-medium text-bone">
                                {{ order.stripe_customer_email ?? 'No disponible' }}
                            </dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4 py-4">
                            <dt class="eyebrow text-bone/60">Fecha</dt>
                            <dd class="text-sm font-medium text-bone">
                                {{ formatDate(order.paid_at) ?? 'Pendiente' }}
                            </dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4 py-4">
                            <dt class="eyebrow text-bone/60">Sesión Stripe</dt>
                            <dd class="max-w-[15rem] truncate font-mono text-xs text-bone/80">
                                {{ order.stripe_session_id }}
                            </dd>
                        </div>
                    </dl>
                </aside>
            </div>

            <div
                v-else
                class="mt-10 border-2 border-ink bg-bone-deep px-6 py-5 text-sm font-medium text-ash-700"
            >
                El pago ha vuelto desde Stripe, pero la orden todavía no aparece. Si
                acabas de pagar, espera unos segundos y recarga.
            </div>

            <div class="mt-10">
                <Link href="/" class="btn-block btn-block-flag">Volver al archivo</Link>
            </div>
        </section>
    </main>
</template>
