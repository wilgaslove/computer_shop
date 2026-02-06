<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({ category: Object })

const form = useForm({ name: props.category.name })

const submit = () => {
  form.put(`/categories/${props.category.id}`)
}
</script>

<template>
  <form @submit.prevent="submit">
    <input v-model="form.name" />
    <button>Modifier</button>
  </form>
</template>
