<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({ name: '' })

const submit = () => {
  form.post('/categories')
}
</script>

<template>
  <form @submit.prevent="submit">
    <input v-model="form.name" placeholder="Nom catégorie" />
    <button>Créer</button>
  </form>
</template>
