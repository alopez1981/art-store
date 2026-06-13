<script setup>
import {Head, Link, usePage} from '@inertiajs/vue3'
import {computed, ref} from 'vue'
import logoJakunaMeliata from '../assets/logo-jakuna-meliata.jpg'


const props = defineProps({
    artwork: {
        type: Object,
        required: true,
    },
    relatedArtworks: {
        type: Array,
        required: true,
    },
    shippingZones: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()
const loading = ref(false)
const errorMessage = ref('')

// Zona de envío seleccionada (por defecto, la primera: Badalona/BCN).
const selectedZone = ref(props.shippingZones[0]?.code ?? null)

const currentZone = computed(
    () => props.shippingZones.find((z) => z.code === selectedZone.value) ?? null,
)

// Total estimado = obra + envío, visible antes de ir a Stripe.
const totalWithShipping = computed(
    () => props.artwork.price + (currentZone.value?.amount ?? 0),
)

function formatAmount(price) {
    return new Intl.NumberFormat('es-ES', {style: 'currency', currency: 'EUR'}).format(price / 100)
}

function indexLabel(i) {
    return String(i + 1).padStart(3, '0')
}

async function buyArtwork() {
    loading.value = true
    errorMessage.value = ''

    try {
        const response = await fetch(`/checkout/${props.artwork.id}`, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN':
                    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
            },
            body: JSON.stringify({shipping_zone: selectedZone.value}),
        })

        const data = await response.json()
        if (!response.ok) throw new Error(data.message || 'No se pudo iniciar el pago.')

        window.location.href = data.url
    } catch (error) {
        errorMessage.value = error.message
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <Head :title="artwork.title"/>

    <main class="min-h-screen bg-bone text-ink">
        <!-- ============================== NAV ============================== -->
        <header class="border-b-2 border-ink bg-bone">
            <div class="mx-auto flex max-w-[1480px] items-center justify-between px-6 py-4 sm:px-10">
                <Link href="/" class="flex items-center gap-3">
                    <img :src="logoJakunaMeliata" alt="Jakuna Meliata" class="h-12 w-auto"/>
                    <span class="eyebrow hidden text-ash-500 md:inline">/ ARCHIVO DE OBRA</span>
                </Link>

                <nav class="flex items-center gap-6 text-ink">
                    <Link href="/#ahora" class="eyebrow hover:text-flag">Ahora</Link>
                    <Link href="/#archivo" class="eyebrow hover:text-flag">Archivo</Link>
                    <Link
                        v-if="!page.props.auth?.user"
                        href="/admin/login"
                        class="eyebrow hover:text-flag"
                    >
                        Admin
                    </Link>
                    <Link v-else href="/admin/artworks" class="eyebrow hover:text-flag">
                        Admin
                    </Link>
                </nav>
            </div>
        </header>

        <!-- ============================== MIGAS ============================== -->
        <div class="border-b-2 border-ink bg-bone">
            <div class="mx-auto flex max-w-[1480px] flex-wrap items-center justify-between gap-4 px-6 py-3 sm:px-10">
                <p class="eyebrow text-ash-500">
                    Archivo /
                    <span class="text-ink">Pieza</span>
                </p>
                <p
                    class="eyebrow"
                    :class="artwork.vendido_at ? 'text-ash-500' : 'text-flag'"
                >
                    {{ artwork.vendido_at ? 'En colección privada' : 'En circulación' }}
                </p>
            </div>
        </div>

        <!-- ============================== HERO FICHA ============================== -->
        <section class="grid border-b-2 border-ink lg:grid-cols-[1.4fr_1fr]">
            <!-- Imagen: pieza dominante, negro absoluto, sin adornos. -->
            <!-- Imagen a tamaño natural, sin recorte. En desktop queda
                 fija (sticky) mientras se hace scroll por el panel de compra,
                 evitando el hueco vacío bajo la foto. -->
            <div class="lg:self-start lg:sticky lg:top-0">
                <img
                    :src="artwork.image_url"
                    :alt="artwork.title"
                    class="w-full border-b-2 border-ink bg-ink object-contain lg:max-h-screen"
                />
            </div>

            <!-- Sidebar editorial: título, descripción, ficha técnica, compra. -->
            <aside class="flex flex-col justify-between bg-bone px-6 py-12 sm:px-10 lg:px-14 lg:py-16">
                <div>
                    <p class="eyebrow text-flag">
                        Mel Atael · {{ artwork.year || 'sin fechar' }}
                    </p>
                    <h1 class="headline mt-6 text-5xl leading-[0.86] text-ink sm:text-6xl lg:text-7xl">
                        {{ artwork.title }}
                    </h1>

                    <p class="mt-8 text-base leading-7 text-ash-700">
                        {{ artwork.description }}
                    </p>

                    <!--
                         Ficha técnica como tabla de archivo:
                         label uppercase a la izquierda, valor sobrio a la derecha,
                         divisor horizontal entre filas. Sin tarjetas pastel.
                    -->
                    <dl class="mt-10 border-t border-ink/30">
                        <div class="grid grid-cols-[120px_1fr] items-baseline gap-4 border-b border-ink/30 py-4">
                            <dt class="eyebrow text-ash-500">Técnica</dt>
                            <dd class="text-sm font-semibold text-ink">
                                {{ artwork.technique || 'Por catalogar' }}
                            </dd>
                        </div>
                        <div class="grid grid-cols-[120px_1fr] items-baseline gap-4 border-b border-ink/30 py-4">
                            <dt class="eyebrow text-ash-500">Dimensiones</dt>
                            <dd class="text-sm font-semibold text-ink">
                                {{ artwork.dimensions || 'Por catalogar' }}
                            </dd>
                        </div>
                        <div class="grid grid-cols-[120px_1fr] items-baseline gap-4 border-b border-ink/30 py-4">
                            <dt class="eyebrow text-ash-500">Año</dt>
                            <dd class="text-sm font-semibold text-ink">
                                {{ artwork.year || 'Por catalogar' }}
                            </dd>
                        </div>
                        <div class="grid grid-cols-[120px_1fr] items-baseline gap-4 py-4">
                            <dt class="eyebrow text-ash-500">Edición</dt>
                            <dd class="text-sm font-semibold text-ink">Pieza única · firmada</dd>
                        </div>
                    </dl>
                </div>

                <!-- ===== Bloque de compra ===== -->
                <div class="mt-12 border-t-2 border-ink pt-8">
                    <div class="flex items-baseline justify-between">
                        <span class="eyebrow text-ash-500">Precio</span>
                        <span class="headline text-4xl text-flag sm:text-5xl">
                            {{ formatAmount(artwork.price) }}
                        </span>
                    </div>

                    <!-- ===== Envío desde Badalona: tarifa visible antes de pagar ===== -->
                    <div
                        v-if="!artwork.vendido_at && shippingZones.length"
                        class="mt-8 border-2 border-ink"
                    >
                        <p class="border-b-2 border-ink bg-ink px-4 py-2 text-[10px] font-extrabold uppercase tracking-[0.22em] text-bone">
                            Envío desde Badalona (Barcelona)
                        </p>
                        <div>
                            <label
                                v-for="zone in shippingZones"
                                :key="zone.code"
                                class="flex cursor-pointer items-baseline justify-between gap-4 border-b border-ink/20 px-4 py-3 transition last:border-b-0 hover:bg-bone-deep"
                                :class="selectedZone === zone.code ? 'bg-bone-deep' : ''"
                            >
                                <span class="flex items-baseline gap-3">
                                    <input
                                        v-model="selectedZone"
                                        type="radio"
                                        name="shipping_zone"
                                        :value="zone.code"
                                        class="accent-flag"
                                    />
                                    <span>
                                        <span class="block text-sm font-semibold text-ink">{{ zone.label }}</span>
                                        <span class="block text-xs text-ash-500">
                                            {{ zone.description }} · {{ zone.min_days }}–{{ zone.max_days }} días laborables
                                        </span>
                                    </span>
                                </span>
                                <span class="eyebrow whitespace-nowrap text-ink">{{ formatAmount(zone.amount) }}</span>
                            </label>
                        </div>
                        <div class="flex items-baseline justify-between border-t-2 border-ink px-4 py-3">
                            <span class="eyebrow text-ash-500">Total con envío</span>
                            <span class="headline text-2xl text-ink">{{ formatAmount(totalWithShipping) }}</span>
                        </div>
                    </div>

                    <div
                        v-if="errorMessage"
                        class="mt-6 border-2 border-flag bg-flag px-4 py-3 text-sm font-bold uppercase tracking-widest text-bone"
                    >
                        {{ errorMessage }}
                    </div>

                    <div class="mt-6 flex flex-col gap-3">
                        <button
                            class="btn-block w-full disabled:cursor-not-allowed"
                            :class="
                                artwork.vendido_at
                                    ? 'border-ash-300 bg-ash-300 text-ash-700'
                                    : 'btn-block-flag'
                            "
                            :disabled="artwork.vendido_at || loading"
                            @click="buyArtwork"
                        >
                            <template v-if="artwork.vendido_at">Pieza en colección privada</template>
                            <template v-else-if="loading">Redirigiendo a Stripe…</template>
                            <template v-else>Adquirir esta pieza</template>
                        </button>

                        <a
                            href="https://www.instagram.com/melataelcorazon?igsh=NHZzdHduY2VlM214"
                            target="_blank"
                            rel="noreferrer"
                            class="btn-block btn-block-outline w-full"
                        >
                            Ver proceso en estudio
                        </a>
                    </div>

                    <p class="mt-4 text-xs leading-5 text-ash-500">
                        Pago seguro vía Stripe. En el pago se solicita dirección de envío
                        y teléfono de contacto. Al completarse la compra, la pieza se
                        registra automáticamente en el archivo como vendida y se prepara
                        el envío desde el estudio en Badalona.
                    </p>
                </div>
            </aside>
        </section>

        <!-- ============================== OTRAS PIEZAS ============================== -->
        <section v-if="relatedArtworks.length" class="border-b-2 border-ink bg-bone">
            <div class="mx-auto max-w-[1480px] px-6 py-16 sm:px-10 lg:py-24">
                <div class="flex flex-col gap-6 border-b-2 border-ink pb-8 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="eyebrow text-flag">Más obra</p>
                        <h2 class="headline mt-4 text-4xl text-ink sm:text-5xl lg:text-6xl">
                            Otras piezas del archivo
                        </h2>
                    </div>
                    <Link href="/" class="btn-block btn-block-outline self-start">
                        Volver al archivo completo
                    </Link>
                </div>

                <div class="mt-10 grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="(item, index) in relatedArtworks"
                        :key="item.id"
                        class="group flex flex-col"
                    >
                        <Link :href="`/artworks/${item.slug}`"
                              class="relative block overflow-hidden border-2 border-ink bg-ink">
                            <img
                                :src="item.image_url"
                                :alt="item.title"
                                class="aspect-[4/5] w-full object-cover transition duration-500 group-hover:scale-[1.02]"
                            />
                            <span
                                v-if="item.vendido_at"
                                class="absolute left-3 top-3 border-2 border-bone bg-flag px-3 py-1 text-[10px] font-extrabold uppercase tracking-[0.22em] text-bone"
                            >
                                Vendida
                            </span>
                        </Link>

                        <div class="mt-5 flex items-baseline justify-between gap-4 border-b border-ink/30 pb-2">
                            <span class="eyebrow text-ash-500">№ {{ indexLabel(index) }}</span>
                            <span class="eyebrow text-ink">{{ formatAmount(item.price) }}</span>
                        </div>

                        <Link :href="`/artworks/${item.slug}`">
                            <h3 class="headline mt-4 text-3xl text-ink transition group-hover:text-flag">
                                {{ item.title }}
                            </h3>
                        </Link>
                        <p class="mt-3 line-clamp-3 text-sm leading-6 text-ash-700">
                            {{ item.description }}
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <!-- ============================== FOOTER MANIFIESTO ============================== -->
        <footer class="bg-bone">
            <div
                class="mx-auto flex max-w-[1480px] flex-col gap-8 px-6 py-12 sm:px-10 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="eyebrow text-flag">Mel Atael · Estudio</p>
                    <p class="headline mt-3 max-w-xl text-3xl text-ink sm:text-4xl">
                        Imprimir, soldar, gritar. Y volver a empezar.
                    </p>
                </div>
                <div class="flex flex-wrap gap-4 self-start">
                    <a
                        href="https://www.instagram.com/melataelcorazon?igsh=NHZzdHduY2VlM214"
                        target="_blank"
                        rel="noreferrer"
                        class="btn-block btn-block-flag"
                    >
                        Instagram
                    </a>
                    <a
                        href="https://www.tiktok.com/@melataelcorazon"
                        target="_blank"
                        rel="noreferrer"
                        class="btn-block btn-block-outline"
                    >
                        TikTok
                    </a>
                </div>
            </div>
        </footer>
    </main>
</template>
