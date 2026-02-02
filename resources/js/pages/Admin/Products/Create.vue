<script setup>
import { useForm, Link } from '@inertiajs/vue3'

// defineProps({ categories: Array })

const props = defineProps({
  categories: Array,
})

const form = useForm({
  name: '',
  price: '',
  stock: '',
  category_id: '',
  description: '',
  image: null,
  active: true,
})

function submit() {
  form.post(route('admin.products.store'), {
    forceFormData: true,
  })
}
</script>

<template>
  <div>
    <h1 class="text-xl font-bold mb-4">Nouveau produit</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <input v-model="form.name" placeholder="Nom" />
      <input v-model="form.price" type="number" placeholder="Prix" />
      <input v-model="form.stock" type="number" placeholder="Stock" />

      <select v-model="form.category_id">
        <option value="">Catégorie</option>
        <option v-for="c in categories" :key="c.id" :value="c.id">
          {{ c.name }}
        </option>
      </select>


      <input type="file" @change="e => form.image = e.target.files[0]" />

      <textarea v-model="form.description" placeholder="Description" />

      <button type="submit" class="btn btn-primary">Créer</button>
    </form>
  </div>
</template>
