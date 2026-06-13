<script setup>
import {Head, Link, usePage} from '@inertiajs/vue3'
import {computed} from 'vue'
import logoJakunaMeliata from '../assets/logo-jakuna-meliata.jpg'

const props = defineProps({
    artworks: {
        type: Array,
        required: true,
    },
})
const page = usePage()

const availableArtworks = computed(() => props.artworks.filter((a) => !a.vendido_at))
const soldArtworks = computed(() => props.artworks.filter((a) => a.vendido_at))

// La obra destacada es la primera disponible; si no hay, la última vendida.
const featuredArtwork = computed(() => availableArtworks.value[0] ?? soldArtworks.value[0] ?? null)
const restAvailable = computed(() => {
    if (!featuredArtwork.value || featuredArtwork.value.vendido_at) {
        return availableArtworks.value
    }
    return availableArtworks.value.filter((a) => a.id !== featuredArtwork.value.id)
})

function formatAmount(price) {
    return new Intl.NumberFormat('es-ES', {style: 'currency', currency: 'EUR'}).format(price / 100)
}

function indexLabel(i) {
    return String(i + 1).padStart(3, '0')
}

</script>

<template>
    <Head title="Catálogo"/>

    <main class="min-h-screen bg-bone text-ink">
        <!-- ============================== NAV ============================== -->
        <header class="border-b-2 border-ink bg-bone">
            <div class="mx-auto flex max-w-[1480px] items-center justify-between px-6 py-4 sm:px-10">
                <Link href="/" class="flex items-center gap-3">
                    <img :src="logoJakunaMeliata" alt="Jakuna Meliata" class="h-12 w-auto"/>
                </Link>

                <nav class="flex items-center gap-6 text-ink">
                    <a href="#ahora" class="eyebrow hover:text-flag"> Obra Disponible</a>
                    <a href="#estudio" class="eyebrow hover:text-flag">Estudio</a>
                    <a href="#archivo" class="eyebrow hover:text-flag">Archivo</a>
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

        <!-- ============================== HERO ============================== -->
        <section class="grid border-b-2 border-ink lg:grid-cols-2">
            <!-- Manifiesto: bloque rojo con titular cartel y CTA. -->
            <div class="relative flex flex-col justify-between bg-flag px-6 py-14 text-bone sm:px-12 lg:px-16 lg:py-20">
                <div>
                    <p class="eyebrow text-bone/80">Me Lata · arte urbano · Desde 2014</p>
                    <h1 class="headline mt-8 text-[14vw] leading-[0.85] sm:text-[10vw] lg:text-[7.2vw]">
                        Me lata
                        <br/>
                        el
                        <br/>
                        corazón
                    </h1>
                    <p class="mt-8 max-w-xl text-base font-medium leading-7 text-bone/90 sm:text-lg">
                        Latas recicladas pintadas con mensaje positivos. Latas que reivindican dede el amor un mundo
                        mejor.
                    </p>
                    <p class="mt-8 max-w-xl text-base font-medium leading-7 text-bone/90 sm:text-lg">
                        Con miles de Latas instaladas
                        en las calles de Barcelona, Girona, Lleida, Mallorca,
                        París, Londres, La Rioja y Madrid… buscando humanizar las ciudades, alegrar los corazones y
                        hacer sonreír a los peatones.
                    </p>
                </div>

                <div class="mt-12 flex flex-wrap items-center gap-4">
                    <a href="#ahora" class="btn-block btn-block-ink">Ver obra disponible</a>
                    <a href="#archivo"
                       class="btn-block btn-block-outline border-bone text-bone hover:bg-bone hover:text-flag">
                        Archivo
                    </a>
                </div>
            </div>

            <!-- Obra destacada: imagen dominante sobre negro, ficha mínima superpuesta. -->
            <div v-if="featuredArtwork" class="relative bg-ink">
                <Link :href="`/artworks/${featuredArtwork.slug}`" class="block h-full">
                    <img
                        :src="featuredArtwork.image_url"
                        :alt="featuredArtwork.title"
                        class="h-full max-h-[720px] w-full object-cover"
                    />
                </Link>

                <div
                    class="absolute inset-x-0 bottom-0 flex flex-col gap-3 border-t-2 border-bone/20 bg-ink/85 px-6 py-6 backdrop-blur-sm sm:flex-row sm:items-end sm:justify-between sm:px-10">
                    <div class="text-bone">
                        <p class="eyebrow text-bone/60">
                            {{ featuredArtwork.vendido_at ? 'Pieza · Archivo' : 'Pieza · En circulación' }}
                        </p>
                        <p class="headline mt-2 text-3xl text-bone sm:text-4xl">
                            {{ featuredArtwork.title }}
                        </p>
                    </div>
                    <Link
                        :href="`/artworks/${featuredArtwork.slug}`"
                        class="btn-block btn-block-flag self-start sm:self-auto"
                    >
                        Ver ficha →
                    </Link>
                </div>
            </div>

            <!-- Fallback si todavía no hay obras: bloque sobrio. -->
            <div v-else class="flex items-center justify-center bg-ink p-16 text-bone">
                <p class="eyebrow text-bone/60">Archivo en preparación</p>
            </div>
        </section>

        <!-- ============================== AHORA / DISPONIBLES ============================== -->
        <section id="ahora" class="border-b-2 border-ink">
            <div class="mx-auto max-w-[1480px] px-6 py-16 sm:px-10 lg:py-24">
                <div class="flex flex-col gap-6 border-b-2 border-ink pb-8 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="eyebrow text-flag"> · {{ availableArtworks.length.toString().padStart(2, '0') }}
                            en circulación</p>
                        <h2 class="headline mt-4 text-5xl sm:text-6xl lg:text-7xl">Obra disponible</h2>
                    </div>
                    
                </div>

                <div v-if="restAvailable.length" class="mt-10 grid gap-x-8 gap-y-14 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="(artwork, index) in restAvailable"
                        :key="artwork.id"
                        class="group flex flex-col"
                    >
                        <Link :href="`/artworks/${artwork.slug}`"
                              class="block overflow-hidden border-2 border-ink bg-ink">
                            <img
                                :src="artwork.image_url"
                                :alt="artwork.title"
                                class="aspect-[4/5] w-full object-cover transition duration-500 group-hover:scale-[1.02]"
                            />
                        </Link>

                        <div class="mt-5 flex items-baseline justify-between gap-4 border-b border-ink/30 pb-2">
                            <span class="eyebrow text-ash-500">№ {{ indexLabel(index) }}</span>
                            <span class="eyebrow text-ink">{{ formatAmount(artwork.price) }}</span>
                        </div>

                        <Link
                            :href="`/artworks/${artwork.slug}`"
                            class="mt-4 block"
                        >
                            <h3 class="headline text-3xl text-ink transition group-hover:text-flag">
                                {{ artwork.title }}
                            </h3>
                        </Link>

                        <p class="mt-3 line-clamp-3 text-sm leading-6 text-ash-700">
                            {{ artwork.description }}
                        </p>

                        <div class="mt-6 flex gap-3">
                            <Link
                                :href="`/artworks/${artwork.slug}`"
                                class="btn-block btn-block-outline flex-1"
                            >
                                Ficha
                            </Link>
                            <!-- La compra se hace desde la ficha: ahí se elige la zona de envío. -->
                            <Link
                                :href="`/artworks/${artwork.slug}`"
                                class="btn-block btn-block-flag flex-1 text-center"
                            >
                                Comprar
                            </Link>
                        </div>
                    </article>
                </div>

                <div v-else class="mt-14 border-2 border-ink bg-bone-deep px-8 py-14 text-center">
                    <p class="eyebrow text-flag">Sin piezas en circulación</p>
                    <p class="headline mt-4 text-3xl text-ink">Todo en archivo</p>
                    <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-ash-700">
                        Próxima tirada en preparación. Mientras tanto, el archivo histórico
                        sigue abierto abajo.
                    </p>
                </div>
            </div>
        </section>

        <!-- ============================== ESTUDIO / PROCESO (vídeo + bio) ============================== -->
        <section id="estudio" class="grid border-b-2 border-ink bg-bone lg:grid-cols-[1fr_1.4fr]">
            <!-- Vídeo vertical del proceso: mudo, en bucle, sin controles. -->
            <div class="relative border-b-2 border-ink bg-ink lg:border-b-0 lg:border-r-2">
                <!-- Rutas con binding para que Vite las sirva desde /public sin intentar empaquetarlas. -->
                <video
                    class="h-full max-h-[760px] w-full object-cover"
                    :src="'/videos/proceso.mp4'"
                    :poster="'/videos/proceso-poster.jpg'"
                    autoplay
                    muted
                    loop
                    playsinline
                ></video>
                <p class="absolute bottom-0 left-0 border-r-2 border-t-2 border-bone/30 bg-ink/85 px-4 py-2 text-[10px] font-extrabold uppercase tracking-[0.22em] text-bone">
                    Estudio · Badalona
                </p>
            </div>

            <!-- Bio manifiesto -->
            <div class="flex flex-col justify-center px-6 py-14 sm:px-12 lg:px-16 lg:py-20">
                <p class="eyebrow text-flag">Quién hay detrás</p>
                <h2 class="headline mt-6 text-5xl text-ink sm:text-6xl lg:text-7xl">
                    Me lata
                    <br/>
                    el corazón
                </h2>
                <div class="mt-8 max-w-2xl space-y-5 text-base leading-7 text-ash-700">
                    <p>
                        «Me Lata el Corazón» es un juego de palabras entre la lata y el
                        latir del corazón: el sentimiento de sentirse vivo, contagiando
                        la idea de estar enamorado de la vida.
                    </p>
                    <p>
                        Con los años de expresión enlatada, las composiciones, mensajes y
                        colores han ido evolucionando hasta convertirse no solo en
                        elementos urbanos pensados para transformar la calle y la
                        sociedad: también en esculturas pensadas para decorar interiores.
                    </p>
                </div>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="#ahora" class="btn-block btn-block-flag">Ver obra disponible</a>
                    <a
                        href="https://www.instagram.com/melataelcorazon?igsh=NHZzdHduY2VlM214"
                        target="_blank"
                        rel="noreferrer"
                        class="btn-block btn-block-outline"
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
        </section>

        <!-- ============================== ARCHIVO / VENDIDAS ============================== -->
        <section v-if="soldArtworks.length" id="archivo" class="bg-ink text-bone">
            <div class="mx-auto max-w-[1480px] px-6 py-16 sm:px-10 lg:py-24">
                <div
                    class="flex flex-col gap-6 border-b-2 border-bone/40 pb-8 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="eyebrow text-flag">Archivo · {{ soldArtworks.length.toString().padStart(2, '0') }}
                            piezas</p>
                        <h2 class="headline mt-4 text-5xl text-bone sm:text-6xl lg:text-7xl">
                            Obras en colección
                        </h2>
                    </div>
                    <p class="max-w-md text-sm font-medium leading-6 text-bone/60">
                        El archivo deja constancia. Cada pieza tuvo destino, dueño y
                        contexto. No vuelven a circulación.
                    </p>
                </div>

                <div class="mt-12 grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="(artwork, index) in soldArtworks"
                        :key="artwork.id"
                        class="group flex flex-col"
                    >
                        <Link :href="`/artworks/${artwork.slug}`"
                              class="relative block overflow-hidden border-2 border-bone/30 bg-ash-900">
                            <img
                                :src="artwork.image_url"
                                :alt="artwork.title"
                                class="aspect-[4/5] w-full object-cover opacity-90 transition duration-500 group-hover:opacity-100"
                            />
                            <span
                                class="absolute left-3 top-3 border-2 border-bone bg-flag px-3 py-1 text-[10px] font-extrabold uppercase tracking-[0.22em] text-bone">
                                Vendida
                            </span>
                        </Link>

                        <div class="mt-5 flex items-baseline justify-between gap-4 border-b border-bone/20 pb-2">
                            <span class="eyebrow text-bone/50">№ {{ indexLabel(index) }}</span>
                            <span class="eyebrow text-bone/70">{{ formatAmount(artwork.price) }}</span>
                        </div>

                        <Link :href="`/artworks/${artwork.slug}`">
                            <h3 class="headline mt-4 text-3xl text-bone transition group-hover:text-flag">
                                {{ artwork.title }}
                            </h3>
                        </Link>
                        <p class="mt-3 line-clamp-3 text-sm leading-6 text-bone/60">
                            {{ artwork.description }}
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <!-- ============================== FOOTER MANIFIESTO ============================== -->
        <footer class="border-t-2 border-ink bg-bone">
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
