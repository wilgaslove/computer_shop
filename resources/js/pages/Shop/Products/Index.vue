<script setup>
import ProductCard from '@/Components/ProductCard.vue'  
import ShopLayout from '@/Layouts/ShopLayout.vue'

defineProps({
    products: {
        type: Object,
        required: true,
    },
})
</script>
<template>

<ShopLayout>

  <h1 class="text-2xl font-bold mb-6">Produits</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <ProductCard
            v-for="product in products.data"
            :key="product.id"
            :product="product"
        />
    </div>
</ShopLayout>
</template>
