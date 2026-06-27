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
        class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden border border-gray-100"
    >
        <!-- Image -->
        <div class="h-56 bg-gray-100 flex items-center justify-center">
            <img
                v-if="product.image"
                :src="`/storage/${product.image}`"
                :alt="product.name"
                class="h-full w-full object-cover"
            />

            <div
                v-else
                class="text-gray-400 text-sm"
            >
                Aucune image
            </div>
        </div>

        <!-- Informations -->
        <div class="p-4">

            <p class="text-xs text-gray-500 mb-1">
                {{ product.category?.name }}
            </p>

            <h2 class="text-lg font-bold text-gray-900 line-clamp-2">
                {{ product.name }}
            </h2>

            <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                {{ product.description }}
            </p>

            <div class="flex items-center justify-between mt-4">

                <span class="text-2xl font-bold text-blue-600">
                    {{ Number(product.price).toLocaleString() }} FCFA
                </span>

                <span
                    v-if="product.stock > 0"
                    class="text-green-600 text-sm font-medium"
                >
                    En stock
                </span>

                <span
                    v-else
                    class="text-red-500 text-sm font-medium"
                >
                    Rupture
                </span>

            </div>

            <Link
                :href="route('shop.products.show', product.id)"
                class="mt-5 w-full inline-flex justify-center items-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition"
            >
                Voir le produit
            </Link>

        </div>
    </div>
</template>