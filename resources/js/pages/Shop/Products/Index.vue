<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, reactive, ref, watch } from 'vue'

import ProductCard from '@/Components/Shop/ProductCard.vue'
import Hero from '@/Components/Shop/Hero.vue'
import ShopLayout from '@/Layouts/ShopLayout.vue'

const props = defineProps({
    products: { type: Object, required: true },
    heroSliders: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
    priceRange: { type: Object, default: () => ({ min: 0, max: 0 }) },
})

const sorts = [
    { value: 'latest', label: 'Nouveautés' },
    { value: 'price_asc', label: 'Prix croissant' },
    { value: 'price_desc', label: 'Prix décroissant' },
    { value: 'name_asc', label: 'Nom (A → Z)' },
]

function fromProps(f) {
    return {
        q: f.q ?? '',
        category: f.category ?? '',
        min_price: f.min_price ?? '',
        max_price: f.max_price ?? '',
        sort: f.sort ?? 'latest',
        in_stock: !!f.in_stock,
    }
}

const form = reactive(fromProps(props.filters))
const showFilters = ref(false)

// Paramètres d'URL minimaux (on n'envoie que ce qui est renseigné)
function buildParams(f) {
    const p = {}
    if (f.q) p.q = f.q
    if (f.category) p.category = f.category
    if (f.min_price !== '' && f.min_price !== null) p.min_price = f.min_price
    if (f.max_price !== '' && f.max_price !== null) p.max_price = f.max_price
    if (f.sort && f.sort !== 'latest') p.sort = f.sort
    if (f.in_stock) p.in_stock = 1
    return p
}

let timer = null

function apply() {
    const next = buildParams(form)

    // Rien n'a changé par rapport à la page affichée : pas de requête inutile.
    if (JSON.stringify(next) === JSON.stringify(buildParams(fromProps(props.filters)))) return

    router.get(route('shop.products'), next, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

// Léger délai : évite une requête à chaque chiffre tapé dans les prix.
watch(form, () => {
    clearTimeout(timer)
    timer = setTimeout(apply, 350)
})

// Quand le serveur renvoie d'autres filtres (ex : recherche depuis la barre du haut), on aligne le formulaire.
watch(() => props.filters, (f) => Object.assign(form, fromProps(f)))

const hasFilters = computed(() => Object.keys(buildParams(form)).length > 0)

const activeCategory = computed(() =>
    props.categories.find(c => String(c.id) === String(form.category))
)

function reset() {
    Object.assign(form, fromProps({}))
}

function formatFcfa(value) {
    return `${Number(value).toLocaleString('fr-FR')} FCFA`
}

const priceChip = computed(() => {
    const min = form.min_price !== '' ? formatFcfa(form.min_price) : null
    const max = form.max_price !== '' ? formatFcfa(form.max_price) : null

    if (min && max) return `${min} – ${max}`
    if (min) return `À partir de ${min}`
    if (max) return `Jusqu'à ${max}`
    return null
})
</script>

<template>
    <Head :title="$page.props.seo?.title ?? 'Boutique'" />

    <ShopLayout>

        <!-- HERO (uniquement sur le catalogue sans filtre) -->
        <Hero :sliders="props.heroSliders" />

        <section class="mx-auto max-w-7xl px-2 py-8 lg:px-4">

            <!-- Titre -->
            <div class="mb-6 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">
                        {{ form.q ? `Résultats pour « ${form.q} »` : (activeCategory ? activeCategory.name : 'Nos produits') }}
                    </h2>
                    <p class="mt-1 text-gray-500">
                        {{ props.products.total }} produit{{ props.products.total > 1 ? 's' : '' }}
                    </p>
                </div>

                <!-- Tri -->
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 lg:hidden"
                        @click="showFilters = !showFilters"
                    >
                        {{ showFilters ? 'Masquer les filtres' : 'Filtres' }}
                    </button>

                    <label class="sr-only" for="sort">Trier par</label>
                    <select
                        id="sort"
                        v-model="form.sort"
                        class="rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option v-for="s in sorts" :key="s.value" :value="s.value">{{ s.label }}</option>
                    </select>
                </div>
            </div>

            <!-- Filtres actifs -->
            <div v-if="hasFilters" class="mb-6 flex flex-wrap items-center gap-2">
                <button
                    v-if="form.q"
                    type="button"
                    class="rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-800 hover:bg-blue-200"
                    @click="form.q = ''"
                >
                    Recherche : {{ form.q }} ✕
                </button>

                <button
                    v-if="activeCategory"
                    type="button"
                    class="rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-800 hover:bg-blue-200"
                    @click="form.category = ''"
                >
                    {{ activeCategory.name }} ✕
                </button>

                <button
                    v-if="priceChip"
                    type="button"
                    class="rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-800 hover:bg-blue-200"
                    @click="form.min_price = ''; form.max_price = ''"
                >
                    {{ priceChip }} ✕
                </button>

                <button
                    v-if="form.in_stock"
                    type="button"
                    class="rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-800 hover:bg-blue-200"
                    @click="form.in_stock = false"
                >
                    En stock ✕
                </button>

                <button type="button" class="text-sm text-gray-500 underline hover:text-blue-600" @click="reset">
                    Tout réinitialiser
                </button>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">

                <!-- Filtres -->
                <aside
                    class="h-fit space-y-6 rounded-2xl border border-gray-200 bg-white p-5 lg:block"
                    :class="showFilters ? 'block' : 'hidden'"
                >
                    <!-- Catégories -->
                    <div>
                        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Catégories</h3>

                        <ul class="space-y-1 text-sm">
                            <li>
                                <button
                                    type="button"
                                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left transition"
                                    :class="form.category === '' ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50'"
                                    @click="form.category = ''"
                                >
                                    Toutes
                                </button>
                            </li>

                            <li v-for="category in props.categories" :key="category.id">
                                <button
                                    type="button"
                                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left transition"
                                    :class="String(form.category) === String(category.id) ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50'"
                                    @click="form.category = String(category.id)"
                                >
                                    <span>{{ category.name }}</span>
                                    <span class="text-xs text-gray-400">{{ category.products_count }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Prix -->
                    <div>
                        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Prix (FCFA)</h3>

                        <div class="flex items-center gap-2">
                            <input
                                v-model="form.min_price"
                                type="number"
                                min="0"
                                inputmode="numeric"
                                :placeholder="Math.floor(props.priceRange.min)"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            <span class="text-gray-400">–</span>
                            <input
                                v-model="form.max_price"
                                type="number"
                                min="0"
                                inputmode="numeric"
                                :placeholder="Math.ceil(props.priceRange.max)"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>
                    </div>

                    <!-- Disponibilité -->
                    <label class="flex cursor-pointer items-center gap-3 text-sm text-gray-700">
                        <input
                            v-model="form.in_stock"
                            type="checkbox"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                        Uniquement les produits en stock
                    </label>
                </aside>

                <!-- Résultats -->
                <div class="lg:col-span-3">

                    <div
                        v-if="props.products.data?.length"
                        class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3"
                    >
                        <ProductCard
                            v-for="product in props.products.data"
                            :key="product.id"
                            :product="product"
                        />
                    </div>

                    <!-- Aucun résultat -->
                    <div v-else class="rounded-2xl border border-gray-200 bg-white py-16 text-center text-gray-500">
                        <div class="mb-4 text-5xl">{{ hasFilters ? '🔍' : '💻' }}</div>

                        <h3 class="text-xl font-semibold text-gray-700">
                            {{ hasFilters ? 'Aucun produit ne correspond à votre recherche' : 'Aucun produit disponible' }}
                        </h3>

                        <p class="mt-2">
                            {{ hasFilters ? 'Essayez d\'autres mots-clés ou retirez certains filtres.' : 'Les produits seront bientôt disponibles.' }}
                        </p>

                        <button
                            v-if="hasFilters"
                            type="button"
                            class="mt-6 rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700"
                            @click="reset"
                        >
                            Réinitialiser les filtres
                        </button>
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

                </div>
            </div>
        </section>

    </ShopLayout>
</template>
