<script setup>
const props = defineProps({
  products: {
    type: Object,
    required: true
  }
})
</script>
<template>

  <p class="text-2xl font-bold mb-4">Nos produits</p>

  <div class="grid grid-cols-4 gap-4">
    <div v-for="product in props.products.data"  :key="product.id">
      <img
        :src="`/storage/${product.image}`"
        class="w-full h-48 object-cover"
      />
      <h3>{{ product.name }}</h3>
      <p>{{ product.price }} FCFA</p>
    </div>
  </div>
</template>
