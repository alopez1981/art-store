<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

function submit() {
    form.post('/admin/login')
}
</script>

<template>
    <Head title="Admin · Acceso" />

    <main class="min-h-screen bg-bone text-ink">
        <header class="border-b-2 border-ink bg-bone">
            <div class="mx-auto flex max-w-[1480px] items-center justify-between px-6 py-4 sm:px-10">
                <Link href="/" class="flex items-baseline gap-3">
                    <span class="headline text-3xl text-flag">MEL</span>
                    <span class="headline text-3xl text-ink">ATAEL</span>
                    <span class="eyebrow hidden text-ash-500 md:inline">/ ADMIN</span>
                </Link>
                <Link href="/" class="eyebrow text-ink hover:text-flag">← Catálogo</Link>
            </div>
        </header>

        <section class="mx-auto max-w-md px-6 py-16 sm:px-10">
            <div class="border-2 border-ink bg-bone p-8">
                <p class="eyebrow text-flag">Acceso restringido</p>
                <h1 class="headline mt-4 text-5xl text-ink">Entrar</h1>
                <p class="mt-4 text-sm leading-6 text-ash-700">
                    Panel interno de gestión de obras, precios y visibilidad del archivo.
                </p>

                <form class="mt-8 space-y-6" @submit.prevent="submit">
                    <label class="block">
                        <span class="eyebrow mb-2 block text-ash-700">Email</span>
                        <input
                            v-model="form.email"
                            type="email"
                            class="w-full border-2 border-ink bg-bone-deep px-4 py-3 text-sm font-medium text-ink outline-none focus:border-flag"
                        />
                        <p v-if="form.errors.email" class="mt-2 text-sm font-bold text-flag">
                            {{ form.errors.email }}
                        </p>
                    </label>

                    <label class="block">
                        <span class="eyebrow mb-2 block text-ash-700">Contraseña</span>
                        <input
                            v-model="form.password"
                            type="password"
                            class="w-full border-2 border-ink bg-bone-deep px-4 py-3 text-sm font-medium text-ink outline-none focus:border-flag"
                        />
                    </label>

                    <label class="flex items-center gap-3 text-sm font-medium text-ash-700">
                        <input v-model="form.remember" type="checkbox" class="h-4 w-4 border-2 border-ink" />
                        Recordarme
                    </label>

                    <button
                        type="submit"
                        class="btn-block btn-block-flag w-full disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Entrando…' : 'Entrar al admin' }}
                    </button>
                </form>
            </div>
        </section>
    </main>
</template>
