<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const form = useForm({ name: '' })

function submit() {
  form.post(route('admin.categories.store'))
}
</script>

<template>
  <div class="mx-auto max-w-lg p-6">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Nouvelle catégorie</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Nom</label>
        <input
          v-model="form.name"
          type="text"
          class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
          placeholder="Ex : Ordinateurs portables"
        />
        <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
      </div>

      <div class="flex gap-4 pt-2">
        <button
          type="submit"
          class="rounded-lg bg-blue-600 px-5 py-2.5 font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
          :disabled="form.processing"
        >
          Créer
        </button>

        <Link
          :href="route('admin.categories.index')"
          class="rounded-lg border border-gray-300 px-5 py-2.5 font-semibold text-gray-700 hover:bg-gray-50"
        >
          Annuler
        </Link>
      </div>
    </form>
  </div>
</template>
