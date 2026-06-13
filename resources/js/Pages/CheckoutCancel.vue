<script setup>
import { Head, Link } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Pago cancelado" />

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

        <section class="mx-auto max-w-3xl px-6 py-16 sm:px-10">
            <p class="eyebrow text-flag">Pago cancelado</p>
            <h1 class="headline mt-4 text-5xl text-ink sm:text-6xl lg:text-7xl">
                No se ha realizado ningún cargo
            </h1>
            <p class="mt-5 max-w-2xl text-base leading-7 text-ash-700">
                Has salido de Stripe antes de completar la compra. La pieza sigue
                disponible salvo que otra persona la adquiera antes.
            </p>

            <div class="mt-10 flex flex-wrap gap-3">
                <Link href="/" class="btn-block btn-block-flag">Volver al archivo</Link>
            </div>
        </section>
    </main>
</template>
