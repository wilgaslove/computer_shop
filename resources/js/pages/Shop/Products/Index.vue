<script setup>
import { Head } from '@inertiajs/vue3'

import ProductCard from '@/Components/Shop/ProductCard.vue'
import Hero from '@/Components/Shop/Hero.vue'
import ShopLayout from '@/Layouts/ShopLayout.vue'


const props = defineProps({
    products: {
        type: Object,
        required: true,
    },

    heroSliders: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>


<Head title="Boutique" />

<ShopLayout>

    <!-- HERO -->
    <Hero
        :sliders="props.heroSliders"
    />


    <!-- PRODUITS -->
    <section
        class="max-w-7xl
               mx-auto
               px-6
               lg:px-8
               py-12"
    >

        <!-- TITRE -->
        <div class="mb-8">

            <h2
                class="text-3xl
                       font-bold
                       text-gray-900"
            >
                Nos produits
            </h2>

            <p class="text-gray-500 mt-2">
                Découvrez nos ordinateurs et équipements.
            </p>

        </div>


        <!-- GRILLE PRODUITS -->
        <div
            v-if="props.products.data?.length"
            class="grid
                   grid-cols-1
                   sm:grid-cols-2
                   lg:grid-cols-3
                   xl:grid-cols-4
                   gap-6"
        >

            <ProductCard
                v-for="product in props.products.data"
                :key="product.id"
                :product="product"
            />

        </div>


        <!-- AUCUN PRODUIT -->
        <div
            v-else
            class="py-16
                   text-center
                   text-gray-500"
        >

            <div class="text-5xl mb-4">
                💻
            </div>

            <h3
                class="text-xl
                       font-semibold
                       text-gray-700"
            >
                Aucun produit disponible
            </h3>

            <p class="mt-2">
                Les produits seront bientôt disponibles.
            </p>

        </div>

    </section>

</ShopLayout>


</template>
