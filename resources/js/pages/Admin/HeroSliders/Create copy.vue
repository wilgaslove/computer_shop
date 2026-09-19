<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    slider: {
        type: Object,
        required: true,
    },
})

const preview = ref(
    props.slider.image
        ? `/storage/${props.slider.image}`
        : null
)

const form = useForm({
    title: props.slider.title ?? '',
    subtitle: props.slider.subtitle ?? '',
    button_text: props.slider.button_text ?? '',
    button_link: props.slider.button_link ?? '',
    position: props.slider.position ?? 0,
    active: Boolean(props.slider.active),
    image: null,
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
    form
        .transform((data) => ({
            ...data,
            _method: 'PUT',
        }))
        .post(route('admin.hero-sliders.update', props.slider.id), {
            forceFormData: true,
        })
}
</script>

<template>

    <Head title="Modifier la bannière" />

    <div class="max-w-5xl mx-auto p-8">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">

            <div>
                <h1 class="text-3xl font-bold">
                    Modifier la bannière
                </h1>

                <p class="text-gray-500 mt-1">
                    Modifiez les informations de cette bannière.
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
                    class="w-full border rounded-xl p-3"
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
                    class="w-full border rounded-xl p-3"
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
                        class="w-full border rounded-xl p-3"
                        placeholder="Acheter maintenant"
                    >
                </div>

                <div>
                    <label class="block font-medium mb-2">
                        Lien du bouton
                    </label>

                    <input
                        v-model="form.button_link"
                        type="text"
                        class="w-full border rounded-xl p-3"
                        placeholder="/shop"
                    >
                </div>

            </div>


            <!-- Image -->
            <div>

                <label class="block font-medium mb-2">
                    Image
                </label>

                <input
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    @change="selectImage"
                    class="w-full border rounded-xl p-3"
                >

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
                    alt="Aperçu bannière"
                    class="w-full max-w-3xl rounded-2xl shadow object-cover"
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
                    class="border rounded-xl p-3 w-40"
                >

                <div
                    v-if="form.errors.position"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.position }}
                </div>

            </div>


            <!-- Active -->
            <div>

                <label class="flex items-center gap-3">

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
            <div class="flex justify-end">

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
                            ? 'Modification...'
                            : 'Enregistrer les modifications'
                    }}
                </button>

            </div>

        </form>

    </div>

</template>