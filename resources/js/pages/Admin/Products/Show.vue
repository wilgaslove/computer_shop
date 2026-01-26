<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  product: Object,
})
</script>

<template>
  <div>
    <h1 class="text-xl font-bold mb-4">Détails du produit</h1>

    <p><strong>Nom :</strong> {{ product.name }}</p>
    <p><strong>Prix :</strong> {{ product.price }} €</p>

    <Link :href="route('admin.products.edit', product.id)" class="btn btn-warning">
      Modifier
    </Link>

    <Link :href="route('admin.products.index')" class="btn">
      Retour
    </Link>
  </div>
</template>
