<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const preview = ref(null)

const form = useForm({
    title: '',
    subtitle: '',
    button_text: '',
    button_link: '',
    image: null,
    position: 0,
    active: true,
})

function selectImage(event) {
    const file = event.target.files[0]

    if (!file) {
        return
    }

    form.image = file
    preview.value = URL.createObjectURL(file)
}

function submit() {
    form.post(route('admin.hero-sliders.store'), {
        forceFormData: true,
    })
}
</script>

<template>
    <Head title="Nouvelle bannière" />

    <div class="max-w-5xl mx-auto p-8">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">

            <div>
                <h1 class="text-3xl font-bold">
                    Nouvelle bannière
                </h1>

                <p class="text-gray-500 mt-1">
                    Créez une nouvelle bannière pour la boutique.
                </p>
            </div>

            <Link
                :href="route('admin.hero-sliders.index')"
                class="text-blue-600 hover:underline"
            >
                ← Retour
            </Link>

        </div>

        <!-- Formulaire -->
        <form
            @submit.prevent="submit"
            class="bg-white rounded-2xl shadow p-8 space-y-6"
        >

            <!-- Titre -->
            <div>
                <label class="block font-medium mb-2">
                    Titre
                </label>

                <input
                    v-model="form.title"
                    type="text"
                    class="w-full border rounded-xl p-3
                           focus:ring-2 focus:ring-blue-500
                           focus:border-blue-500"
                    placeholder="Ex : Découvrez nos nouveaux ordinateurs"
                >

                <div
                    v-if="form.errors.title"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.title }}
                </div>
            </div>

            <!-- Sous-titre -->
            <div>
                <label class="block font-medium mb-2">
                    Sous-titre
                </label>

                <input
                    v-model="form.subtitle"
                    type="text"
                    class="w-full border rounded-xl p-3
                           focus:ring-2 focus:ring-blue-500
                           focus:border-blue-500"
                    placeholder="Ex : Les meilleures offres du moment"
                >

                <div
                    v-if="form.errors.subtitle"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.subtitle }}
                </div>
            </div>

            <!-- Bouton -->
            <div class="grid md:grid-cols-2 gap-6">

                <div>
                    <label class="block font-medium mb-2">
                        Texte du bouton
                    </label>

                    <input
                        v-model="form.button_text"
                        type="text"
                        class="w-full border rounded-xl p-3
                               focus:ring-2 focus:ring-blue-500
                               focus:border-blue-500"
                        placeholder="Acheter maintenant"
                    >

                    <div
                        v-if="form.errors.button_text"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.button_text }}
                    </div>
                </div>

                <div>
                    <label class="block font-medium mb-2">
                        Lien du bouton
                    </label>

                    <input
                        v-model="form.button_link"
                        type="text"
                        class="w-full border rounded-xl p-3
                               focus:ring-2 focus:ring-blue-500
                               focus:border-blue-500"
                        placeholder="/shop"
                    >

                    <div
                        v-if="form.errors.button_link"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.button_link }}
                    </div>
                </div>

            </div>

            <!-- Image -->
            <div>

                <label class="block font-medium mb-2">
                    Image de la bannière
                </label>

                <input
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    @change="selectImage"
                    class="w-full border rounded-xl p-3"
                >

                <p class="text-sm text-gray-500 mt-2">
                    Formats acceptés : JPG, PNG, WEBP — maximum 4 Mo.
                </p>

                <div
                    v-if="form.errors.image"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.image }}
                </div>

            </div>

            <!-- Preview -->
            <div v-if="preview">

                <p class="font-medium mb-2">
                    Aperçu
                </p>

                <img
                    :src="preview"
                    alt="Aperçu de la bannière"
                    class="w-full max-w-3xl h-64
                           object-cover rounded-2xl shadow"
                >

            </div>

            <!-- Position -->
            <div>

                <label class="block font-medium mb-2">
                    Ordre d'affichage
                </label>

                <input
                    v-model.number="form.position"
                    type="number"
                    min="0"
                    class="border rounded-xl p-3 w-40
                           focus:ring-2 focus:ring-blue-500
                           focus:border-blue-500"
                >

                <p class="text-sm text-gray-500 mt-1">
                    0 = première bannière.
                </p>

                <div
                    v-if="form.errors.position"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.position }}
                </div>

            </div>

            <!-- Active -->
            <div>

                <label class="flex items-center gap-3 cursor-pointer">

                    <input
                        v-model="form.active"
                        type="checkbox"
                        class="rounded"
                    >

                    <span>
                        Bannière active
                    </span>

                </label>

            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3">

                <Link
                    :href="route('admin.hero-sliders.index')"
                    class="px-6 py-3 rounded-xl
                           border border-gray-300
                           hover:bg-gray-50"
                >
                    Annuler
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="bg-blue-600 hover:bg-blue-700
                           text-white px-8 py-3
                           rounded-xl font-semibold
                           disabled:opacity-50"
                >
                    {{
                        form.processing
                            ? 'Création...'
                            : 'Créer la bannière'
                    }}
                </button>

            </div>

        </form>

    </div>
</template>