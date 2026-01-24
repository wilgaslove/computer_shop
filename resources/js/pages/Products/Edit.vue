<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  product: Object,
})

const form = useForm({
  name: props.product.name,
  price: props.product.price,
})

const submit = () => {
  form.put(route('admin.products.update', props.product.id))
}
</script>

<template>
  <div>
    <h1 class="text-xl font-bold mb-4">Modifier produit</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label>Nom</label>
        <input v-model="form.name" class="input" />
      </div>

      <div>
        <label>Prix</label>
        <input type="number" v-model="form.price" class="input" />
      </div>

      <button class="btn btn-primary">Mettre à jour</button>

      <Link :href="route('admin.products.index')" class="btn">
        Annuler
      </Link>
    </form>
  </div>
</template>
