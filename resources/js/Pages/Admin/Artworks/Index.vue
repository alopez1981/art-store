<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'

defineProps({
    artworks: {
        type: Array,
        required: true,
    },
})

const page = usePage()

function formatAmount(price) {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(price / 100)
}

function destroyArtwork(artwork) {
    if (!window.confirm(`Eliminar "${artwork.title}"?`)) {
        return
    }
    router.delete(`/admin/artworks/${artwork.id}`)
}
</script>

<template>
    <Head title="Admin · Obras" />

    <main class="min-h-screen bg-bone text-ink">
        <header class="border-b-2 border-ink bg-bone">
            <div class="mx-auto flex max-w-[1480px] items-center justify-between px-6 py-4 sm:px-10">
                <Link href="/" class="flex items-baseline gap-3">
                    <span class="headline text-3xl text-flag">MEL</span>
                    <span class="headline text-3xl text-ink">ATAEL</span>
                    <span class="eyebrow hidden text-ash-500 md:inline">/ ADMIN</span>
                </Link>
                <div class="flex items-center gap-6">
                    <Link href="/admin/orders" class="eyebrow text-ink hover:text-flag">Pedidos</Link>
                    <Link href="/" class="eyebrow text-ink hover:text-flag">Catálogo</Link>
                    <button class="eyebrow text-ink hover:text-flag" @click="router.post('/admin/logout')">
                        Salir
                    </button>
                </div>
            </div>
        </header>

        <section class="mx-auto max-w-[1480px] px-6 py-12 sm:px-10">
            <div class="flex flex-col gap-6 border-b-2 border-ink pb-8 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="eyebrow text-flag">Admin · Catálogo</p>
                    <h1 class="headline mt-4 text-5xl text-ink sm:text-6xl">Gestión de obras</h1>
                    <p class="mt-3 max-w-md text-sm leading-6 text-ash-700">
                        Crea, edita y controla qué piezas se muestran en el archivo público.
                    </p>
                </div>
                <Link href="/admin/artworks/create" class="btn-block btn-block-flag self-start">
                    + Nueva obra
                </Link>
            </div>

            <div
                v-if="page.props.flash?.success"
                class="my-8 border-2 border-flag bg-flag px-5 py-4 text-sm font-bold uppercase tracking-widest text-bone"
            >
                {{ page.props.flash.success }}
            </div>

            <div class="mt-10 overflow-x-auto border-2 border-ink bg-bone">
                <table class="min-w-full">
                    <thead class="border-b-2 border-ink bg-bone-deep">
                        <tr class="text-left">
                            <th class="eyebrow px-5 py-4 text-ash-700">Obra</th>
                            <th class="eyebrow px-5 py-4 text-ash-700">Slug</th>
                            <th class="eyebrow px-5 py-4 text-ash-700">Precio</th>
                            <th class="eyebrow px-5 py-4 text-ash-700">Estado</th>
                            <th class="eyebrow px-5 py-4 text-ash-700">Técnica</th>
                            <th class="eyebrow px-5 py-4 text-ash-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/20">
                        <tr v-for="artwork in artworks" :key="artwork.id" class="align-top">
                            <td class="px-5 py-5">
                                <div class="flex items-start gap-4">
                                    <img
                                        :src="artwork.image_url"
                                        :alt="artwork.title"
                                        class="h-20 w-20 border-2 border-ink object-cover"
                                    />
                                    <div>
                                        <p class="headline text-xl text-ink">{{ artwork.title }}</p>
                                        <p class="mt-1 text-sm text-ash-700">
                                            {{ artwork.dimensions || 'Sin dimensiones' }}
                                        </p>
                                        <p
                                            class="eyebrow mt-2"
                                            :class="artwork.vendido_at ? 'text-ash-500' : 'text-flag'"
                                        >
                                            {{ artwork.vendido_at ? 'Vendida' : 'Disponible' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-5 text-sm text-ash-700">{{ artwork.slug }}</td>
                            <td class="px-5 py-5 text-sm font-bold text-ink">
                                {{ formatAmount(artwork.price) }}
                            </td>
                            <td class="px-5 py-5">
                                <span
                                    class="eyebrow inline-flex border-2 px-3 py-1"
                                    :class="
                                        artwork.is_published
                                            ? 'border-ink bg-bone text-ink'
                                            : 'border-ash-300 bg-bone-deep text-ash-500'
                                    "
                                >
                                    {{ artwork.is_published ? 'Publicada' : 'Oculta' }}
                                </span>
                            </td>
                            <td class="px-5 py-5 text-sm text-ash-700">
                                {{ artwork.technique || 'Sin técnica' }}
                            </td>
                            <td class="px-5 py-5">
                                <div class="flex flex-wrap gap-2">
                                    <Link
                                        :href="`/artworks/${artwork.slug}`"
                                        class="eyebrow border-2 border-ink px-3 py-2 text-ink hover:bg-ink hover:text-bone"
                                    >
                                        Ver
                                    </Link>
                                    <Link
                                        :href="`/admin/artworks/${artwork.id}/edit`"
                                        class="eyebrow border-2 border-ink bg-ink px-3 py-2 text-bone hover:bg-ash-900"
                                    >
                                        Editar
                                    </Link>
                                    <button
                                        class="eyebrow border-2 border-flag bg-flag px-3 py-2 text-bone hover:bg-flag-deep"
                                        @click="destroyArtwork(artwork)"
                                    >
                                        Borrar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</template>
