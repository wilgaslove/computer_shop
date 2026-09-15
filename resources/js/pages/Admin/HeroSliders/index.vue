<script setup>
import { Head, Link, router } from '@inertiajs/vue3'

defineProps({
    sliders: {
        type: Array,
        required: true,
    },
})

function toggleSlider(id) {
    router.post(
        route('admin.hero-sliders.toggle', id),
        {},
        {
            preserveScroll: true,
        }
    )
}

function deleteSlider(id) {
    if (!confirm('Voulez-vous vraiment supprimer cette bannière ?')) {
        return
    }

    router.delete(
        route('admin.hero-sliders.destroy', id),
        {
            preserveScroll: true,
        }
    )
}
</script>

<template>

    <Head title="Bannières" />

    <div class="max-w-7xl mx-auto p-8">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">

            <div>
                <h1 class="text-3xl font-bold">
                    Bannières
                </h1>

                <p class="text-gray-500 mt-1">
                    Gérez les bannières affichées sur la boutique.
                </p>
            </div>

            <Link
                :href="route('admin.hero-sliders.create')"
                class="bg-blue-600 hover:bg-blue-700
                       text-white px-5 py-3
                       rounded-xl font-semibold"
            >
                + Nouvelle bannière
            </Link>

        </div>


        <!-- Table -->
        <div class="bg-white rounded-2xl shadow overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-50 border-b">

                    <tr>

                        <th class="text-left p-4">
                            Image
                        </th>

                        <th class="text-left p-4">
                            Bannière
                        </th>

                        <th class="text-center p-4">
                            Position
                        </th>

                        <th class="text-center p-4">
                            Statut
                        </th>

                        <th class="text-right p-4">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <tr
                        v-for="slider in sliders"
                        :key="slider.id"
                        class="border-b last:border-0"
                    >

                        <!-- Image -->
                        <td class="p-4">

                            <img
                                :src="`/storage/${slider.image}`"
                                :alt="slider.title"
                                class="w-32 h-20 object-cover rounded-xl"
                            >

                        </td>


                        <!-- Informations -->
                        <td class="p-4">

                            <div class="font-semibold">
                                {{ slider.title }}
                            </div>

                            <div
                                v-if="slider.subtitle"
                                class="text-sm text-gray-500"
                            >
                                {{ slider.subtitle }}
                            </div>

                        </td>


                        <!-- Position -->
                        <td class="p-4 text-center">

                            <span
                                class="inline-flex items-center
                                       justify-center
                                       w-10 h-10
                                       rounded-full
                                       bg-gray-100
                                       font-semibold"
                            >
                                {{ slider.position }}
                            </span>

                        </td>


                        <!-- Statut -->
                        <td class="p-4 text-center">

                            <button
                                @click="toggleSlider(slider.id)"
                                class="px-3 py-1 rounded-full text-sm font-medium"
                                :class="
                                    slider.active
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-gray-100 text-gray-500'
                                "
                            >
                                {{
                                    slider.active
                                        ? 'Active'
                                        : 'Inactive'
                                }}
                            </button>

                        </td>


                        <!-- Actions -->
                        <td class="p-4">

                            <div class="flex justify-end gap-2">

                                <Link
                                    :href="
                                        route(
                                            'admin.hero-sliders.edit',
                                            slider.id
                                        )
                                    "
                                    class="px-4 py-2
                                           bg-blue-100
                                           text-blue-700
                                           rounded-lg
                                           hover:bg-blue-200"
                                >
                                    Modifier
                                </Link>


                                <button
                                    @click="deleteSlider(slider.id)"
                                    class="px-4 py-2
                                           bg-red-100
                                           text-red-700
                                           rounded-lg
                                           hover:bg-red-200"
                                >
                                    Supprimer
                                </button>

                            </div>

                        </td>

                    </tr>


                    <!-- Aucun slider -->
                    <tr v-if="sliders.length === 0">

                        <td
                            colspan="5"
                            class="p-12 text-center text-gray-500"
                        >
                            Aucune bannière disponible.

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</template>