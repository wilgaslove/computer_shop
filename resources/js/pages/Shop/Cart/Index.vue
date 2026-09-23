<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    total: {
        type: [Number, String],
        default: 0,
    },
})

function formatFcfa(value) {
    return `${Number(value).toLocaleString('fr-FR')} FCFA`
}

function updateQuantity(item, quantity) {
    quantity = Math.max(1, Math.min(quantity, item.product.stock || quantity))

    router.patch(
        route('cart.update', item.product.id),
        { quantity },
        { preserveScroll: true }
    )
}

function removeItem(item) {
    router.delete(route('cart.destroy', item.product.id), {
        preserveScroll: true,
    })
}

function clearCart() {
    if (!confirm('Vider entièrement le panier ?')) return

    router.delete(route('cart.clear'), { preserveScroll: true })
}
</script>

<template>
    <Head title="Mon panier" />

    <ShopLayout>

        <section class="bg-gray-50 py-12">

            <div class="mx-auto max-w-5xl px-6 lg:px-8">

                <div class="mb-8 flex items-center justify-between">

                    <h1 class="text-3xl font-bold text-gray-900">
                        Mon panier
                    </h1>

                    <button
                        v-if="props.items.length"
                        type="button"
                        class="text-sm font-medium text-red-600 hover:text-red-700"
                        @click="clearCart"
                    >
                        Vider le panier
                    </button>

                </div>

                <!-- Panier vide -->
                <div
                    v-if="!props.items.length"
                    class="rounded-2xl border border-gray-200 bg-white py-16 text-center"
                >
                    <div class="text-5xl">
                        🛒
                    </div>

                    <h3 class="mt-4 text-xl font-semibold text-gray-700">
                        Votre panier est vide
                    </h3>

                    <p class="mt-2 text-gray-500">
                        Parcourez la boutique pour ajouter des produits.
                    </p>

                    <Link
                        :href="route('shop.products.index')"
                        class="mt-6 inline-block rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-700"
                    >
                        Voir les produits
                    </Link>
                </div>

                <!-- Contenu du panier -->
                <div v-else class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                    <!-- Articles -->
                    <div class="lg:col-span-2 space-y-4">

                        <div
                            v-for="item in props.items"
                            :key="item.product.id"
                            class="flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-4 sm:flex-row sm:items-center"
                        >

                            <!-- Image -->
                            <Link
                                :href="route('shop.products.show', item.product.id)"
                                class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-xl bg-gray-100"
                            >
                                <img
                                    v-if="item.product.cover_image"
                                    :src="`/storage/${item.product.cover_image}`"
                                    :alt="item.product.name"
                                    class="h-full w-full object-cover"
                                >
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center text-3xl text-gray-300"
                                >
                                    💻
                                </div>
                            </Link>

                            <!-- Infos -->
                            <div class="flex-1">
                                <Link
                                    :href="route('shop.products.show', item.product.id)"
                                    class="font-semibold text-gray-900 hover:text-blue-600"
                                >
                                    {{ item.product.name }}
                                </Link>

                                <p class="mt-1 text-sm text-gray-500">
                                    {{ formatFcfa(item.product.price) }} / unité
                                </p>

                                <p
                                    v-if="item.product.stock > 0 && item.quantity >= item.product.stock"
                                    class="mt-1 text-xs text-amber-600"
                                >
                                    Quantité maximale en stock atteinte
                                </p>
                            </div>

                            <!-- Quantité -->
                            <div class="flex items-center rounded-xl border border-gray-300">
                                <button
                                    type="button"
                                    class="px-3 py-2 text-lg font-semibold text-gray-600 hover:text-blue-600"
                                    @click="updateQuantity(item, item.quantity - 1)"
                                >
                                    −
                                </button>

                                <span class="w-10 text-center font-semibold text-gray-900">
                                    {{ item.quantity }}
                                </span>

                                <button
                                    type="button"
                                    class="px-3 py-2 text-lg font-semibold text-gray-600 hover:text-blue-600"
                                    @click="updateQuantity(item, item.quantity + 1)"
                                >
                                    +
                                </button>
                            </div>

                            <!-- Sous-total -->
                            <div class="w-32 text-right font-bold text-blue-700">
                                {{ formatFcfa(item.subtotal) }}
                            </div>

                            <!-- Supprimer -->
                            <button
                                type="button"
                                class="text-gray-400 hover:text-red-600"
                                title="Retirer du panier"
                                @click="removeItem(item)"
                            >
                                🗑️
                            </button>

                        </div>

                    </div>

                    <!-- Résumé -->
                    <div class="h-fit rounded-2xl border border-gray-200 bg-white p-6">

                        <h2 class="text-lg font-semibold text-gray-900">
                            Résumé
                        </h2>

                        <div class="mt-4 flex items-center justify-between text-gray-600">
                            <span>Total</span>
                            <span class="text-xl font-bold text-blue-700">
                                {{ formatFcfa(props.total) }}
                            </span>
                        </div>

                        <Link
                            :href="route('shop.products.index')"
                            class="mt-6 block rounded-xl border border-gray-300 py-3 text-center font-semibold text-gray-700 hover:bg-gray-50"
                        >
                            ← Continuer mes achats
                        </Link>

                    </div>

                </div>

            </div>

        </section>

    </ShopLayout>
</template>
