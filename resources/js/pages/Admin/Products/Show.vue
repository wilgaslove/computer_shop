<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    product: { type: Object, required: true },
})

function formatFcfa(value) {
    return `${Number(value).toLocaleString('fr-FR')} FCFA`
}
</script>

<template>
    <Head :title="product.name" />

    <AdminLayout>
        <Link :href="route('admin.products.index')" class="text-sm text-gray-500 hover:text-blue-600">
            ← Retour aux produits
        </Link>

        <div class="mb-6 mt-3 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold text-gray-900">{{ product.name }}</h1>

            <Link
                :href="route('admin.products.edit', product.id)"
                class="rounded-xl bg-blue-600 px-5 py-2.5 font-semibold text-white transition hover:bg-blue-700"
            >
                Modifier
            </Link>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-2">
                <div class="aspect-video overflow-hidden rounded-xl bg-gray-100">
                    <img
                        v-if="product.cover_image"
                        :src="`/storage/${product.cover_image}`"
                        :alt="product.name"
                        class="h-full w-full object-contain"
                    >
                    <div v-else class="flex h-full items-center justify-center text-5xl text-gray-300">💻</div>
                </div>

                <div v-if="product.images?.length" class="mt-4 grid grid-cols-4 gap-3 sm:grid-cols-6">
                    <div v-for="image in product.images" :key="image.id" class="aspect-square overflow-hidden rounded-lg bg-gray-100">
                        <img :src="`/storage/${image.path}`" alt="" class="h-full w-full object-cover">
                    </div>
                </div>

                <h2 class="mb-2 mt-6 text-lg font-semibold text-gray-900">Description</h2>
                <p class="whitespace-pre-line text-sm text-gray-600">{{ product.description || 'Aucune description.' }}</p>
            </div>

            <div class="h-fit space-y-3 rounded-2xl bg-white p-6 text-sm shadow-sm">
                <div class="flex justify-between"><span class="text-gray-500">Prix</span><span class="font-semibold">{{ formatFcfa(product.price) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Stock</span><span class="font-semibold">{{ product.stock }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Catégorie</span><span class="font-semibold">{{ product.category?.name ?? '—' }}</span></div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Statut</span>
                    <span class="font-semibold">{{ product.active ? 'Actif' : 'Masqué' }}</span>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
