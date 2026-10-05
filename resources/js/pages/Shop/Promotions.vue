<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import ProductCard from '@/Components/Shop/ProductCard.vue'
import ShopLayout from '@/Layouts/ShopLayout.vue'

const props = defineProps({
    products: { type: Object, required: true },
    maxDiscount: { type: Number, default: 0 },
    sort: { type: String, default: 'discount' },
    sorts: { type: Array, default: () => [] },
})

function changeSort(event) {
    const value = event.target.value

    router.get(
        route('shop.promotions'),
        value === 'discount' ? {} : { sort: value },
        { preserveScroll: true, preserveState: true, replace: true },
    )
}
</script>

<template>
    <Head :title="$page.props.seo?.title ?? 'Promotions'" />

    <ShopLayout>
        <!-- Bannière -->
        <section class="mb-8 overflow-hidden rounded-2xl bg-slate-900 px-6 py-10 text-white sm:px-10">
            <p class="text-sm font-semibold text-red-400">Offres à durée limitée</p>

            <h1 class="mt-2 text-3xl font-extrabold sm:text-4xl">🔥 Promotions</h1>

            <p class="mt-3 max-w-xl text-slate-300">
                <template v-if="props.products.total > 0">
                    {{ props.products.total }} produit{{ props.products.total > 1 ? 's' : '' }} en promotion
                    <template v-if="props.maxDiscount > 0">
                        , jusqu'à <strong class="text-white">-{{ props.maxDiscount }}%</strong>
                    </template>.
                </template>
                <template v-else>
                    Aucune promotion pour le moment, revenez bientôt.
                </template>
            </p>
        </section>

        <!-- Barre de tri -->
        <div v-if="props.products.data.length" class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-gray-600">
                {{ props.products.from }}–{{ props.products.to }} sur {{ props.products.total }} produits
            </p>

            <label class="flex items-center gap-2 text-sm text-gray-600">
                Trier par
                <select
                    :value="props.sort"
                    class="rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                    @change="changeSort"
                >
                    <option v-for="s in props.sorts" :key="s.value" :value="s.value">{{ s.label }}</option>
                </select>
            </label>
        </div>

        <!-- Produits -->
        <div
            v-if="props.products.data.length"
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        >
            <ProductCard
                v-for="product in props.products.data"
                :key="product.id"
                :product="product"
            />
        </div>

        <!-- Aucune promotion -->
        <div v-else class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center">
            <div class="text-5xl">🏷️</div>
            <h2 class="mt-4 text-xl font-bold text-gray-900">Pas de promotion en ce moment</h2>
            <p class="mt-2 text-gray-500">Découvrez en attendant tout notre catalogue.</p>

            <Link
                :href="route('shop.products')"
                class="mt-6 inline-block rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-700"
            >
                Voir tous les produits
            </Link>
        </div>

        <!-- Pagination -->
        <div v-if="props.products.links?.length > 3" class="mt-10 flex flex-wrap justify-center gap-1">
            <template v-for="(link, i) in props.products.links" :key="i">
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
    </ShopLayout>
</template>
