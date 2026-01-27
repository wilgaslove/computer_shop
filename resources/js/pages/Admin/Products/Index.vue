<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  products: Object,
  can: Object,
})
</script>

<template>
  <div class="p-6 mt-[15%]">
    <h1 class="text-2xl font-bold mb-4">Produits</h1>

    <Link
      v-if="can.create"
      :href="route('admin.products.create')"
      class="btn btn-primary mb-4"
    >
      + Nouveau produit
    </Link>

    <table class="w-full border">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Prix</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="product in products.data" :key="product.id">
          <td>{{ product.name }}</td>
          <td>{{ product.price }} FCFA</td>
          <td class="space-x-2">
            <Link
              v-if="can.edit"
              :href="route('admin.products.edit', product.id)"
            >
              ✏️
            </Link>

            <Link
              v-if="can.delete"
              method="delete"
              as="button"
              :href="route('admin.products.destroy', product.id)"
            >
              🗑️
            </Link>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
