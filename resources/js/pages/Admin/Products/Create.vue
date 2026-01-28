<!-- <script setup>
import { useForm, Link } from '@inertiajs/vue3'

defineProps({
  categories: Array,
})

const form = useForm({
  name: '',
  price: '',
  category_id: '',
  description: '',
  active: true,
})
</script> -->

<script setup>
import { useForm, Link } from '@inertiajs/vue3'

defineProps({ categories: Array })

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
  <!-- <div>
    <h1 class="text-2xl font-bold mb-6">Nouveau produit</h1>

    <form @submit.prevent="form.post(route('admin.products.store'))" class="space-y-4">

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
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
        <div v-if="form.errors.category_id" class="text-red-500">
          {{ form.errors.category_id }}
        </div>
      </div>

      <div>
        <label>Description</label>
        <textarea v-model="form.description" class="input"></textarea>
      </div>

      <div class="flex gap-4">
        <button
          type="submit"
          class="bg-green-600 text-white px-4 py-2 rounded"
          :disabled="form.processing"
        >
          Enregistrer
        </button>

        <Link
          :href="route('admin.products.index')"
          class="px-4 py-2 border rounded"
        >
          Annuler
        </Link>
      </div>

    </form>
  </div> -->

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

      <button class="btn btn-primary">Créer</button>
    </form>
  </div>
</template>
