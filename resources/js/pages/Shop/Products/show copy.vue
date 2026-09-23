<script setup>
import { Head, Link } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <Head :title="props.product.name" />

    <ShopLayout>

        <!-- Breadcrumb -->
        <section class="border-b border-gray-200 bg-white">
            <div
                class="mx-auto max-w-7xl px-6 py-4 lg:px-8"
            >
                <div class="flex items-center gap-2 text-sm text-gray-500">

                    <Link
                        :href="route('shop.products.index')"
                        class="hover:text-blue-600"
                    >
                        Boutique
                    </Link>

                    <span>
                        /
                    </span>

                    <span class="text-gray-900">
                        {{ props.product.name }}
                    </span>

                </div>
            </div>
        </section>

        <!-- Produit -->
        <section class="bg-gray-50 py-12">

            <div
                class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-6 lg:grid-cols-2 lg:px-8"
            >

                <!-- Image -->
                <div>

                    <div
                        class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm"
                    >

                        <div
                            v-if="props.product.image"
                            class="aspect-square"
                        >
                            <img
                                :src="`/storage/${props.product.image}`"
                                :alt="props.product.name"
                                class="h-full w-full object-cover"
                            >
                        </div>

                        <div
                            v-else
                            class="flex aspect-square items-center justify-center"
                        >
                            <div class="text-center text-gray-400">

                                <div class="text-7xl">
                                    💻
                                </div>

                                <p class="mt-4">
                                    Aucune image disponible
                                </p>

                            </div>
                        </div>

                    </div>

                </div>

                <!-- Informations -->
                <div class="flex flex-col justify-center">

                    <!-- Catégorie -->
                    <p
                        v-if="props.product.category"
                        class="text-sm font-semibold uppercase tracking-wide text-blue-600"
                    >
                        {{ props.product.category.name }}
                    </p>

                    <!-- Nom -->
                    <h1
                        class="mt-3 text-3xl font-bold tracking-tight text-gray-900 md:text-4xl"
                    >
                        {{ props.product.name }}
                    </h1>

                    <!-- Prix -->
                    <div class="mt-6">

                        <span
                            class="text-4xl font-bold text-blue-700"
                        >
                            {{
                                Number(props.product.price)
                                    .toLocaleString('fr-FR')
                            }}
                            FCFA
                        </span>

                    </div>

                    <!-- Stock -->
                    <div class="mt-5">

                        <span
                            v-if="props.product.stock > 0"
                            class="inline-flex items-center rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700"
                        >
                            ✓ Disponible
                        </span>

                        <span
                            v-else
                            class="inline-flex items-center rounded-full bg-red-100 px-4 py-2 text-sm font-semibold text-red-700"
                        >
                            Rupture de stock
                        </span>

                    </div>

                    <!-- Description -->
                    <div class="mt-8">

                        <h2
                            class="text-lg font-semibold text-gray-900"
                        >
                            Description
                        </h2>

                        <p
                            v-if="props.product.description"
                            class="mt-3 leading-7 text-gray-600"
                        >
                            {{ props.product.description }}
                        </p>

                        <p
                            v-else
                            class="mt-3 text-gray-500"
                        >
                            Aucune description disponible.
                        </p>

                    </div>

                    <!-- Stock -->
                    <div
                        v-if="props.product.stock > 0"
                        class="mt-8 rounded-xl border border-gray-200 bg-white p-4"
                    >
                        <p class="text-sm text-gray-500">
                            Quantité disponible
                        </p>

                        <p
                            class="mt-1 text-xl font-bold text-gray-900"
                        >
                            {{ props.product.stock }} unité(s)
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">

                        <button
                            type="button"
                            :disabled="props.product.stock <= 0"
                            class="flex-1 rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            🛒 Ajouter au panier
                        </button>

                        <button
                            type="button"
                            class="rounded-xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-50"
                        >
                            ❤️
                        </button>

                    </div>

                    <!-- Retour -->
                    <Link
                        :href="route('shop.products.index')"
                        class="mt-6 inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600"
                    >
                        ← Retour à la boutique
                    </Link>

                </div>

            </div>

        </section>

    </ShopLayout>
</template>