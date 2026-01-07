<script setup>
defineProps({ products: Array })
</script>

<template>
  <div>
    <h1 class="text-xl font-bold mb-4">Produits</h1>

    <a href="/products/create" class="btn">Ajouter</a>

    <ul>
      <li v-for="p in products" :key="p.id">
        {{ p.name }} - {{ p.price }} € ({{ p.category.name }})
      </li>
    </ul>
  </div>
</template>
