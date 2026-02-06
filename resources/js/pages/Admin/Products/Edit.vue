<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  product: Object,
  categories: Array,
})

const form = useForm({
  name: props.product.name,
  price: props.product.price,
  category_id: props.product.category_id,
  description: props.product.description,
  active: props.product.active,
})
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">Modifier le produit</h1>

    <form
      @submit.prevent="form.put(route('admin.products.update', props.product.id))"
      class="space-y-4"
    >

      <div>
        <label>Nom</label>
        <input v-model="form.name" class="input" />
        <div v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</div>
      </div>

      <div>
        <label>Prix</label>
        <input type="number" v-model="form.price" class="input" />
        <div v-if="form.errors.price" class="text-red-500">{{ form.errors.price }}</div>
      </div>

      <div>
        <label>Catégorie</label>
        <select v-model="form.category_id" class="input">
          <option value="">-- Choisir --</option>
          <option
            v-for="cat in categories"
            :key="cat.id"
            :value="cat.id"
          >
            {{ cat.name }}
          </option>
        </select>
      </div>

      <div>
        <label>Description</label>
        <textarea v-model="form.description" class="input"></textarea>
      </div>

      <div class="flex gap-4">
        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded"
          :disabled="form.processing"
        >
          Mettre à jour
        </button>

        <Link
          :href="route('admin.products.index')"
          class="px-4 py-2 border rounded"
        >
          Annuler
        </Link>
      </div>

    </form>
  </div>
</template>
