<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
    artwork: {
        type: Object,
        default: null,
    },
})

/*
 * El form lleva dos fuentes posibles para la imagen:
 *   - image_file: archivo subido desde el ordenador (prioritario).
 *   - image_url:  URL externa o URL ya almacenada en BD (fallback).
 *
 * Inertia detecta automáticamente que image_file es un File
 * y serializa el payload como multipart/form-data.
 */
const form = useForm({
    title: props.artwork?.title ?? '',
    slug: props.artwork?.slug ?? '',
    description: props.artwork?.description ?? '',
    price: props.artwork?.price ?? 0,
    image_file: null,
    image_url: props.artwork?.image_url ?? '',
    technique: props.artwork?.technique ?? '',
    dimensions: props.artwork?.dimensions ?? '',
    year: props.artwork?.year ?? '',
    is_published: props.artwork?.is_published ?? true,
})

// Preview en cliente del archivo seleccionado (data URL).
const localPreview = ref(null)
const fileInputRef = ref(null)

// Si hay archivo nuevo, manda; si no, cae a la URL del campo.
const previewUrl = computed(() => localPreview.value ?? form.image_url ?? null)

function onFileSelected(event) {
    const file = event.target.files?.[0] ?? null
    form.image_file = file

    if (!file) {
        localPreview.value = null
        return
    }

    const reader = new FileReader()
    reader.onload = (e) => {
        localPreview.value = e.target?.result ?? null
    }
    reader.readAsDataURL(file)
}

function clearFile() {
    form.image_file = null
    localPreview.value = null
    if (fileInputRef.value) {
        fileInputRef.value.value = ''
    }
}

function submit() {
    /*
     * PUT no soporta multipart en HTML nativo, así que cuando hay
     * archivo en una edición usamos method spoofing: POST + _method=put.
     * En el resto de casos, los métodos limpios de Inertia.
     */
    if (props.artwork) {
        if (form.image_file) {
            form
                .transform((data) => ({ ...data, _method: 'put' }))
                .post(`/admin/artworks/${props.artwork.id}`, {
                    forceFormData: true,
                    onSuccess: clearFile,
                })
            return
        }
        form.put(`/admin/artworks/${props.artwork.id}`)
        return
    }
    form.post('/admin/artworks', { onSuccess: clearFile })
}
</script>

<template>
    <Head :title="artwork ? 'Editar obra' : 'Nueva obra'" />

    <main class="min-h-screen bg-bone text-ink">
        <header class="border-b-2 border-ink bg-bone">
            <div class="mx-auto flex max-w-[1480px] items-center justify-between px-6 py-4 sm:px-10">
                <Link href="/" class="flex items-baseline gap-3">
                    <span class="headline text-3xl text-flag">MEL</span>
                    <span class="headline text-3xl text-ink">ATAEL</span>
                    <span class="eyebrow hidden text-ash-500 md:inline">/ ADMIN</span>
                </Link>
                <div class="flex items-center gap-6">
                    <Link href="/admin/artworks" class="eyebrow text-ink hover:text-flag">
                        ← Volver
                    </Link>
                    <button class="eyebrow text-ink hover:text-flag" @click="router.post('/admin/logout')">
                        Salir
                    </button>
                </div>
            </div>
        </header>

        <section class="mx-auto max-w-4xl px-6 py-12 sm:px-10">
            <div class="border-b-2 border-ink pb-8">
                <p class="eyebrow text-flag">{{ artwork ? 'Editar pieza' : 'Nueva pieza' }}</p>
                <h1 class="headline mt-4 text-5xl text-ink sm:text-6xl">
                    {{ artwork ? artwork.title : 'Catalogar nueva obra' }}
                </h1>
            </div>

            <form class="mt-10 border-2 border-ink bg-bone p-6 sm:p-10" @submit.prevent="submit">
                <div class="grid gap-6 md:grid-cols-2">
                    <label class="block md:col-span-2">
                        <span class="eyebrow mb-2 block text-ash-700">Título</span>
                        <input
                            v-model="form.title"
                            type="text"
                            class="w-full border-2 border-ink bg-bone-deep px-4 py-3 text-sm font-medium outline-none focus:border-flag"
                        />
                        <p v-if="form.errors.title" class="mt-2 text-sm font-bold text-flag">
                            {{ form.errors.title }}
                        </p>
                    </label>

                    <label class="block md:col-span-2">
                        <span class="eyebrow mb-2 block text-ash-700">Slug</span>
                        <input
                            v-model="form.slug"
                            type="text"
                            class="w-full border-2 border-ink bg-bone-deep px-4 py-3 text-sm font-medium outline-none focus:border-flag"
                        />
                        <p class="mt-2 text-xs text-ash-500">
                            Si lo dejas vacío, se genera desde el título.
                        </p>
                        <p v-if="form.errors.slug" class="mt-2 text-sm font-bold text-flag">
                            {{ form.errors.slug }}
                        </p>
                    </label>

                    <label class="block md:col-span-2">
                        <span class="eyebrow mb-2 block text-ash-700">Descripción</span>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="w-full border-2 border-ink bg-bone-deep px-4 py-3 text-sm font-medium outline-none focus:border-flag"
                        ></textarea>
                    </label>

                    <label class="block">
                        <span class="eyebrow mb-2 block text-ash-700">Precio (céntimos)</span>
                        <input
                            v-model="form.price"
                            type="number"
                            min="0"
                            class="w-full border-2 border-ink bg-bone-deep px-4 py-3 text-sm font-medium outline-none focus:border-flag"
                        />
                    </label>

                    <label class="block">
                        <span class="eyebrow mb-2 block text-ash-700">Año</span>
                        <input
                            v-model="form.year"
                            type="number"
                            min="1900"
                            max="2100"
                            class="w-full border-2 border-ink bg-bone-deep px-4 py-3 text-sm font-medium outline-none focus:border-flag"
                        />
                    </label>

                    <!--
                         Bloque imagen.
                         Izquierda: preview cuadrado (lee data URL del FileReader
                         si hay archivo, o la image_url si no).
                         Derecha: subida de archivo + URL fallback editorial.
                    -->
                    <div class="md:col-span-2">
                        <span class="eyebrow mb-3 block text-ash-700">Imagen</span>

                        <div class="grid gap-6 md:grid-cols-[220px_1fr]">
                            <div class="relative aspect-square overflow-hidden border-2 border-ink bg-bone-deep">
                                <img
                                    v-if="previewUrl"
                                    :src="previewUrl"
                                    alt="Preview"
                                    class="h-full w-full object-cover"
                                />
                                <div v-else class="flex h-full w-full items-center justify-center px-3 text-center">
                                    <p class="eyebrow text-ash-500">Sin imagen</p>
                                </div>
                                <span
                                    v-if="form.image_file"
                                    class="absolute left-3 top-3 border-2 border-bone bg-flag px-2 py-1 text-[10px] font-extrabold uppercase tracking-[0.22em] text-bone"
                                >
                                    Nuevo
                                </span>
                            </div>

                            <div class="space-y-5">
                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <label class="btn-block btn-block-outline cursor-pointer">
                                            {{ form.image_file ? 'Cambiar archivo' : 'Subir archivo' }}
                                            <input
                                                ref="fileInputRef"
                                                type="file"
                                                accept="image/jpeg,image/png,image/webp"
                                                class="hidden"
                                                @change="onFileSelected"
                                            />
                                        </label>
                                        <button
                                            v-if="form.image_file"
                                            type="button"
                                            class="eyebrow text-flag hover:text-flag-deep"
                                            @click="clearFile"
                                        >
                                            Quitar
                                        </button>
                                    </div>
                                    <p v-if="form.image_file" class="mt-3 text-xs font-medium text-ink">
                                        {{ form.image_file.name }}
                                        <span class="text-ash-500">
                                            · {{ Math.round(form.image_file.size / 1024) }} KB
                                        </span>
                                    </p>
                                    <p class="mt-2 text-xs text-ash-500">
                                        JPG, PNG o WEBP. Máximo 5 MB.
                                    </p>
                                    <p
                                        v-if="form.errors.image_file"
                                        class="mt-2 text-sm font-bold text-flag"
                                    >
                                        {{ form.errors.image_file }}
                                    </p>
                                    <div
                                        v-if="form.progress"
                                        class="mt-3 h-1 w-full bg-ink/15"
                                    >
                                        <div
                                            class="h-full bg-flag transition-all"
                                            :style="{ width: `${form.progress.percentage}%` }"
                                        ></div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 text-ash-500">
                                    <span class="h-px flex-1 bg-ink/20"></span>
                                    <span class="eyebrow text-ash-500">o pega una URL</span>
                                    <span class="h-px flex-1 bg-ink/20"></span>
                                </div>

                                <div>
                                    <input
                                        v-model="form.image_url"
                                        type="text"
                                        placeholder="https://…"
                                        :disabled="!!form.image_file"
                                        class="w-full border-2 border-ink bg-bone-deep px-4 py-3 text-sm font-medium outline-none focus:border-flag disabled:cursor-not-allowed disabled:opacity-50"
                                    />
                                    <p
                                        v-if="form.image_file"
                                        class="mt-2 text-xs text-ash-500"
                                    >
                                        Se usará el archivo subido. La URL se ignora mientras haya archivo.
                                    </p>
                                    <p
                                        v-if="form.errors.image_url"
                                        class="mt-2 text-sm font-bold text-flag"
                                    >
                                        {{ form.errors.image_url }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <label class="block">
                        <span class="eyebrow mb-2 block text-ash-700">Técnica</span>
                        <input
                            v-model="form.technique"
                            type="text"
                            class="w-full border-2 border-ink bg-bone-deep px-4 py-3 text-sm font-medium outline-none focus:border-flag"
                        />
                    </label>

                    <label class="block">
                        <span class="eyebrow mb-2 block text-ash-700">Dimensiones</span>
                        <input
                            v-model="form.dimensions"
                            type="text"
                            class="w-full border-2 border-ink bg-bone-deep px-4 py-3 text-sm font-medium outline-none focus:border-flag"
                        />
                    </label>

                    <label class="flex items-center gap-3 md:col-span-2">
                        <input v-model="form.is_published" type="checkbox" class="h-4 w-4 border-2 border-ink" />
                        <span class="eyebrow text-ash-700">Publicar en catálogo</span>
                    </label>
                </div>

                <div class="mt-10 flex flex-wrap gap-3 border-t-2 border-ink pt-8">
                    <button
                        type="submit"
                        class="btn-block btn-block-flag disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Guardando…' : artwork ? 'Guardar cambios' : 'Crear obra' }}
                    </button>
                    <Link href="/admin/artworks" class="btn-block btn-block-outline">
                        Cancelar
                    </Link>
                </div>
            </form>
        </section>
    </main>
</template>
