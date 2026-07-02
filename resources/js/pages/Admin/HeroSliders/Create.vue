<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

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

function selectImage(e) {

    const file = e.target.files[0]

    if (!file) return

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

<div class="max-w-5xl mx-auto p-8">

    <div class="flex justify-between mb-8">

        <h1 class="text-3xl font-bold">

            Nouvelle Bannière

        </h1>

        <Link
            :href="route('admin.hero-sliders.index')"
            class="text-blue-600"
        >
            Retour
        </Link>

    </div>

    <form
        @submit.prevent="submit"
        class="space-y-6"
    >

        <div>

            <label>Titre</label>

            <input
                v-model="form.title"
                class="w-full border rounded p-3"
            >

        </div>

        <div>

            <label>Sous-titre</label>

            <input
                v-model="form.subtitle"
                class="w-full border rounded p-3"
            >

        </div>

        <div>

            <label>Texte du bouton</label>

            <input
                v-model="form.button_text"
                class="w-full border rounded p-3"
            >

        </div>

        <div>

            <label>Lien du bouton</label>

            <input
                v-model="form.button_link"
                class="w-full border rounded p-3"
            >

        </div>

        <div>

            <label>Image</label>

            <input
                type="file"
                @change="selectImage"
            >

        </div>

        <img
            v-if="preview"
            :src="preview"
            class="rounded-xl w-full max-w-lg shadow"
        >

        <div>

            <label>Ordre d'affichage</label>

            <input
                type="number"
                v-model="form.position"
                class="border rounded p-3"
            >

        </div>

        <div>

            <label class="flex items-center gap-3">

                <input
                    type="checkbox"
                    v-model="form.active"
                >

                Active

            </label>

        </div>

        <button
            class="bg-blue-600 text-white px-8 py-3 rounded-xl"
        >

            Enregistrer

        </button>

    </form>

</div>

</template>