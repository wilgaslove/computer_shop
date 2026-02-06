<script setup>
import { router } from '@inertiajs/vue3'

defineProps({ categories: Array })

const destroy = id => {
  if (confirm('Supprimer cette catégorie ?')) {
    router.delete(`/categories/${id}`)
  }
}
</script>

<template>
  <div>
    <h1 class="text-xl font-bold mb-4">Catégories</h1>

    <a href="/categories/create">+ Ajouter</a>

    <ul>
      <li v-for="c in categories" :key="c.id">
        {{ c.name }}
        <a :href="`/categories/${c.id}/edit`">✏️</a>
        <button @click="destroy(c.id)">🗑️</button>
      </li>
    </ul>
  </div>
</template>
