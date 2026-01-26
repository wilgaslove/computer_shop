<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  products: {
    type: Array,
    required: true,
  },
  can: {
    type: Object,
    default: () => ({
      create: false,
      edit: false,
      delete: false,
    }),
  },
})

</script>


<template>
  <div>
    <!-- <pre>{{ can }} </pre> -->
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
          <td>{{ product.price }}F CFA</td> 
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
