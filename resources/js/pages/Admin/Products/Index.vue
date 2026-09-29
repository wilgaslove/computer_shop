<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    products: { type: Object, required: true },
    can: { type: Object, default: () => ({}) },
})

function formatFcfa(value) {
    return `${Number(value).toLocaleString('fr-FR')} FCFA`
}

function destroy(product) {
    if (!confirm(`Supprimer « ${product.name} » ? Cette action est définitive.`)) return

    router.delete(route('admin.products.destroy', product.id), { preserveScroll: true })
}
</script>

<template>
    <Head title="Produits" />

    <AdminLayout>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Produits</h1>
                <p class="text-sm text-gray-500">{{ products.total }} produit(s)</p>
            </div>

            <Link
                v-if="can.create"
                :href="route('admin.products.create')"
                class="rounded-xl bg-blue-600 px-5 py-2.5 font-semibold text-white transition hover:bg-blue-700"
            >
                + Nouveau produit
            </Link>
        </div>

        <div class="overflow-x-auto rounded-2xl bg-white shadow-sm">
            <table class="w-full min-w-[720px] text-sm">
                <thead class="border-b bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="p-4">Produit</th>
                        <th class="p-4">Catégorie</th>
                        <th class="p-4">Prix</th>
                        <th class="p-4">Stock</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="!products.data.length">
                        <td colspan="6" class="p-10 text-center text-gray-500">Aucun produit pour le moment.</td>
                    </tr>

                    <tr v-for="product in products.data" :key="product.id" class="border-b last:border-0 hover:bg-gray-50">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg bg-gray-100">
                                    <img
                                        v-if="product.cover_image"
                                        :src="`/storage/${product.cover_image}`"
                                        :alt="product.name"
                                        class="h-full w-full object-cover"
                                    >
                                    <div v-else class="flex h-full w-full items-center justify-center text-gray-300">💻</div>
                                </div>
                                <Link :href="route('admin.products.show', product.id)" class="font-medium text-gray-900 hover:text-blue-600">
                                    {{ product.name }}
                                </Link>
                            </div>
                        </td>

                        <td class="p-4 text-gray-600">{{ product.category?.name ?? '—' }}</td>
                        <td class="whitespace-nowrap p-4 font-semibold">
                            <template v-if="product.is_on_promotion">
                                <span class="text-red-600">{{ formatFcfa(product.promo_price) }}</span>
                                <span class="block text-xs font-normal text-gray-400 line-through">{{ formatFcfa(product.price) }}</span>
                            </template>
                            <template v-else>{{ formatFcfa(product.price) }}</template>
                        </td>

                        <td class="p-4">
                            <span :class="product.stock <= 0 ? 'font-semibold text-red-600' : product.stock <= 5 ? 'font-semibold text-amber-600' : 'text-gray-700'">
                                {{ product.stock }}
                            </span>
                        </td>

                        <td class="p-4">
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-semibold"
                                :class="product.active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'"
                            >
                                {{ product.active ? 'Actif' : 'Masqué' }}
                            </span>
                        </td>

                        <td class="space-x-4 whitespace-nowrap p-4 text-right">
                            <Link v-if="can.edit" :href="route('admin.products.edit', product.id)" class="font-medium text-blue-600 hover:underline">
                                Modifier
                            </Link>
                            <button v-if="can.delete" type="button" class="font-medium text-red-600 hover:underline" @click="destroy(product)">
                                Supprimer
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="products.links?.length > 3" class="mt-6 flex flex-wrap justify-center gap-1">
            <template v-for="(link, i) in products.links" :key="i">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    preserve-scroll
                    class="rounded-lg px-3 py-2 text-sm"
                    :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                    v-html="link.label"
                />
                <span v-else class="px-3 py-2 text-sm text-gray-300" v-html="link.label" />
            </template>
        </div>
    </AdminLayout>
</template>
