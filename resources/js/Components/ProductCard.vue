<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    product: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <div
        class="group bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
    >
        <!-- Badge -->
        <div class="absolute z-10 m-3">
            <span
                v-if="product.stock > 0"
                class="bg-green-600 text-white text-xs px-3 py-1 rounded-full"
            >
                Disponible
            </span>

            <span
                v-else
                class="bg-red-600 text-white text-xs px-3 py-1 rounded-full"
            >
                Rupture
            </span>
        </div>

        <!-- Image -->
        <div class="relative h-60 bg-gray-100 overflow-hidden">

            <img
                v-if="product.image"
                :src="`/storage/${product.image}`"
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
            />

            <div
                v-else
                class="flex items-center justify-center h-full text-gray-400"
            >
                Aucune image
            </div>

            <!-- Favoris -->
            <button
                class="absolute top-3 right-3 bg-white rounded-full shadow p-2 hover:bg-red-50"
            >
                ❤️
            </button>

        </div>

        <!-- Informations -->
        <div class="p-5">

            <p class="text-sm text-blue-600 font-semibold">
                {{ product.category?.name }}
            </p>

            <h2
                class="mt-2 text-lg font-bold text-gray-900 line-clamp-2"
            >
                {{ product.name }}
            </h2>

            <p
                class="mt-2 text-sm text-gray-500 line-clamp-2"
            >
                {{ product.description }}
            </p>

            <div class="mt-4 flex justify-between items-center">

                <span
                    class="text-2xl font-bold text-blue-700"
                >
                    {{ Number(product.price).toLocaleString() }} FCFA
                </span>

                <span
                    class="text-xs text-gray-500"
                >
                    Stock :
                    <strong>{{ product.stock }}</strong>
                </span>

            </div>

            <div class="mt-5 grid grid-cols-2 gap-2">

                <Link
                    :href="route('shop.products.show', product.id)"
                    class="text-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition"
                >
                    Voir
                </Link>

                <button
                    class="bg-gray-900 hover:bg-black text-white py-2 rounded-lg transition"
                    :disabled="product.stock <= 0"
                >
                    🛒 Panier
                </button>

            </div>

        </div>
    </div>
</template>