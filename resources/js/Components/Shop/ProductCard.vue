<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
})

const adding = ref(false)
const imageFailed = ref(false)

function addToCart() {
    if (props.product.stock <= 0 || adding.value) return

    adding.value = true

    router.post(
        route('cart.store', props.product.id),
        { quantity: 1 },
        {
            preserveScroll: true,
            onFinish: () => { adding.value = false },
            onError: (errors) => { console.error('Erreur ajout panier :', errors) },
        }
    )
}
</script>

<template>
    <article
        class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
    >
        <!-- Badge disponibilité -->
        <div class="absolute left-3 top-3 z-10">
            <span
                v-if="product.stock > 0"
                class="rounded-full bg-green-600 px-3 py-1 text-xs font-semibold text-white"
            >
                Disponible
            </span>

            <span
                v-else
                class="rounded-full bg-red-600 px-3 py-1 text-xs font-semibold text-white"
            >
                Rupture
            </span>
        </div>

        <!-- Image -->
        <Link
            :href="route('shop.products.show', product.id)"
            class="block"
        >
            <div class="relative h-52 overflow-hidden bg-gray-100 sm:h-60">

                <img
                    v-if="product.cover_image && !imageFailed"
                    :src="`/storage/${product.cover_image}`"
                    :alt="product.name"
                    loading="lazy"
                    decoding="async"
                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                    @error="imageFailed = true"
                />

                <div
                    v-else
                    class="flex h-full items-center justify-center text-gray-400"
                >
                    <div class="text-center">
                        <div class="text-4xl">
                            💻
                        </div>

                        <p class="mt-2 text-sm">
                            Aucune image
                        </p>
                    </div>
                </div>

                <!-- Réduction -->
                <span
                    v-if="product.is_on_promotion"
                    class="absolute bottom-3 left-3 rounded-full bg-red-600 px-3 py-1 text-sm font-bold text-white shadow"
                >
                    -{{ product.discount_percent }}%
                </span>

                <!-- Favoris -->
                <button
                    type="button"
                    class="absolute right-3 top-3 rounded-full bg-white p-2.5 shadow transition hover:bg-red-50"
                    @click.prevent
                >
                    ❤️
                </button>
            </div>
        </Link>

        <!-- Informations -->
        <div class="p-4 sm:p-5">

            <!-- Catégorie -->
            <p
                v-if="product.category"
                class="text-sm font-semibold text-blue-600"
            >
                {{ product.category.name }}
            </p>

            <!-- Nom -->
            <Link
                :href="route('shop.products.show', product.id)"
                class="mt-2 block"
            >
                <h2
                    class="line-clamp-2 text-lg font-bold text-gray-900 transition hover:text-blue-600"
                >
                    {{ product.name }}
                </h2>
            </Link>

            <!-- Description -->
            <p
                v-if="product.description"
                class="mt-2 line-clamp-2 text-sm text-gray-500"
            >
                {{ product.description }}
            </p>

            <!-- Prix / stock -->
            <div class="mt-4 flex items-center justify-between gap-3">

                <div>
                    <span
                        class="text-xl font-bold"
                        :class="product.is_on_promotion ? 'text-red-600' : 'text-blue-700'"
                    >
                        {{ Number(product.current_price).toLocaleString('fr-FR') }}
                        FCFA
                    </span>

                    <span
                        v-if="product.is_on_promotion"
                        class="block text-sm text-gray-400 line-through"
                    >
                        {{ Number(product.price).toLocaleString('fr-FR') }} FCFA
                    </span>
                </div>

                <span class="text-xs text-gray-500">
                    Stock :
                    <strong>
                        {{ product.stock }}
                    </strong>
                </span>

            </div>

            <!-- Actions -->
            <div class="mt-5 grid grid-cols-2 gap-2">

                <Link
                    :href="route('shop.products.show', product.id)"
                    class="rounded-lg bg-blue-600 py-2 text-center font-medium text-white transition hover:bg-blue-700"
                >
                    Voir
                </Link>

                <button
                    type="button"
                    class="rounded-lg bg-gray-900 py-2 font-medium text-white transition hover:bg-black disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="product.stock <= 0 || adding"
                    @click="addToCart"
                >
                    {{ adding ? '...' : '🛒 Panier' }}
                </button>

            </div>

        </div>
    </article>
</template>