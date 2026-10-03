<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({ stats: Object })
</script>

<template>
  <AdminLayout>
    <h1 class="text-2xl font-bold mb-6">Dashboard Admin</h1>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6">
      <div class="bg-white p-4 shadow rounded">
        <p class="text-gray-500">Produits</p>
        <p class="text-3xl font-bold">{{ stats.products }}</p>
      </div>

      <div class="bg-white p-4 shadow rounded">
        <p class="text-gray-500">Catégories</p>
        <p class="text-3xl font-bold">{{ stats.categories }}</p>
      </div>
    </div>
  </AdminLayout>
</template>
