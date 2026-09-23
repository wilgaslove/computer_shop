<script setup>
import { useForm, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  product: Object,
  categories: Array,
})

const form = useForm({
  name: props.product.name,
  price: props.product.price,
  stock: props.product.stock,
  category_id: props.product.category_id,
  description: props.product.description,
  image: null,
  images: [],
  active: props.product.active,
})

const coverPreview = ref(null)
const galleryPreviews = ref([])

function onCoverChange(e) {
  const file = e.target.files[0] ?? null
  form.image = file
  coverPreview.value = file ? URL.createObjectURL(file) : null
}

function onGalleryChange(e) {
  const files = Array.from(e.target.files ?? [])
  form.images = files
  galleryPreviews.value = files.map((f) => URL.createObjectURL(f))
}

function removeGalleryFile(index) {
  const files = [...form.images]
  files.splice(index, 1)
  form.images = files

  const previews = [...galleryPreviews.value]
  previews.splice(index, 1)
  galleryPreviews.value = previews
}

function submit() {
  form.put(route('admin.products.update', props.product.id), {
    forceFormData: true,
  })
}

function deleteExistingImage(image) {
  if (!confirm('Supprimer cette image de la galerie ?')) return

  router.delete(
    route('admin.products.images.destroy', [props.product.id, image.id]),
    { preserveScroll: true }
  )
}
</script>

<template>
  <div class="mx-auto max-w-3xl p-6">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Modifier le produit</h1>

    <form @submit.prevent="submit" class="space-y-6">

      <!-- Nom -->
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Nom</label>
        <input
          v-model="form.name"
          type="text"
          class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
        />
        <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
      </div>

      <!-- Prix / Stock -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Prix (FCFA)</label>
          <input
            v-model="form.price"
            type="number"
            step="0.01"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
          />
          <div v-if="form.errors.price" class="mt-1 text-sm text-red-600">{{ form.errors.price }}</div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Stock</label>
          <input
            v-model="form.stock"
            type="number"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
          />
          <div v-if="form.errors.stock" class="mt-1 text-sm text-red-600">{{ form.errors.stock }}</div>
        </div>
      </div>

      <!-- Catégorie -->
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Catégorie</label>
        <select
          v-model="form.category_id"
          class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
        >
          <option value="">-- Choisir --</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
        <div v-if="form.errors.category_id" class="mt-1 text-sm text-red-600">{{ form.errors.category_id }}</div>
      </div>

      <!-- Description -->
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Description</label>
        <textarea
          v-model="form.description"
          rows="4"
          class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
        ></textarea>
      </div>

      <!-- Image de couverture -->
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Image de couverture</label>

        <div v-if="product.image && !coverPreview" class="mb-3">
          <img :src="`/storage/${product.image}`" class="h-32 w-32 rounded-lg border object-cover" />
        </div>

        <input type="file" accept="image/*" @change="onCoverChange" />

        <div v-if="coverPreview" class="mt-3">
          <img :src="coverPreview" class="h-32 w-32 rounded-lg border object-cover" />
        </div>
        <div v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{ form.errors.image }}</div>
      </div>

      <!-- Galerie existante -->
      <div v-if="product.images?.length">
        <label class="mb-2 block text-sm font-medium text-gray-700">Galerie actuelle</label>

        <div class="grid grid-cols-4 gap-3">
          <div
            v-for="image in product.images"
            :key="image.id"
            class="group relative"
          >
            <img :src="`/storage/${image.path}`" class="h-24 w-full rounded-lg border object-cover" />

            <button
              type="button"
              class="absolute -right-2 -top-2 hidden h-6 w-6 items-center justify-center rounded-full bg-red-600 text-xs text-white group-hover:flex"
              @click="deleteExistingImage(image)"
            >
              ✕
            </button>
          </div>
        </div>
      </div>

      <!-- Ajouter des images -->
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Ajouter des images à la galerie</label>

        <input type="file" accept="image/*" multiple @change="onGalleryChange" />

        <div v-if="galleryPreviews.length" class="mt-3 grid grid-cols-4 gap-3">
          <div
            v-for="(src, index) in galleryPreviews"
            :key="index"
            class="group relative"
          >
            <img :src="src" class="h-24 w-full rounded-lg border object-cover" />

            <button
              type="button"
              class="absolute -right-2 -top-2 hidden h-6 w-6 items-center justify-center rounded-full bg-red-600 text-xs text-white group-hover:flex"
              @click="removeGalleryFile(index)"
            >
              ✕
            </button>
          </div>
        </div>
      </div>

      <!-- Actif -->
      <div class="flex items-center gap-2">
        <input id="active" v-model="form.active" type="checkbox" class="h-4 w-4 rounded border-gray-300" />
        <label for="active" class="text-sm font-medium text-gray-700">Produit actif (visible en boutique)</label>
      </div>

      <!-- Actions -->
      <div class="flex gap-4 pt-2">
        <button
          type="submit"
          class="rounded-lg bg-blue-600 px-5 py-2.5 font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
          :disabled="form.processing"
        >
          Mettre à jour
        </button>

        <Link
          :href="route('admin.products.index')"
          class="rounded-lg border border-gray-300 px-5 py-2.5 font-semibold text-gray-700 hover:bg-gray-50"
        >
          Annuler
        </Link>
      </div>

    </form>
  </div>
</template>
