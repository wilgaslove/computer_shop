<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({ categories: Array })

const form = useForm({
  name: '',
  price: '',
  stock: 0,
  category_id: '',
  description: '',
  image: null,
})

const submit = () => {
  form.post('/products')
}
</script>

<template>
  <form @submit.prevent="submit">
    <input v-model="form.name" placeholder="Nom" />
    <input v-model="form.price" type="number" />
    <input v-model="form.stock" type="number" />

    <select v-model="form.category_id">
      <option v-for="c in categories" :value="c.id">
        {{ c.name }}
      </option>
    </select>

    <input type="file" @change="e => form.image = e.target.files[0]" />

    <button>Enregistrer</button>
  </form>
</template>
