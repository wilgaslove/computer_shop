<script setup>
import ProductCard from '@/Components/Shop/ProductCard.vue'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import Hero from '@/Components/Shop/Hero.vue'

defineProps({
    products: {
        type: Object,
        // sliders: Array,
        required: true, 
    },
    sliders: {
        type: Array,
        required: true,
    }
})
</script>
<template>

    <ShopLayout>
        <Hero :sliders="sliders" />



        <h1 class="text-2xl font-bold mb-6">Produits disponible</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
        </div>
    </ShopLayout>
</template>
