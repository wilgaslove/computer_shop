<script setup>
import { Link } from '@inertiajs/vue3'
</script>
<template>
  <div class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-4">Bienvenue sur notre boutique</h1>
      <p class="mb-4">Découvrez nos derniers produits et profitez de nos offres spéciales.</p>
      <Link href="/products" class="btn btn-primary">Voir les produits</Link>
    </div>
  </div>
</template>