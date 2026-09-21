<script setup>
import { Head, Link, router } from '@inertiajs/vue3'

const props = defineProps({
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

        <!-- HEADER -->
        <div class="flex items-center justify-between mb-8">

            <div>
                <h1 class="text-3xl font-bold text-gray-900">
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
                       rounded-xl font-semibold
                       transition"
            >
                + Nouvelle bannière
            </Link>

        </div>


        <!-- LISTE -->
        <div class="bg-white rounded-2xl shadow overflow-hidden">

            <!-- TABLEAU -->
            <div class="overflow-x-auto">

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

                        <!-- BANNIÈRES -->
                        <tr
                            v-for="slider in props.sliders"
                            :key="slider.id"
                            class="border-b last:border-0 hover:bg-gray-50"
                        >

                            <!-- IMAGE -->
                            <td class="p-4">

                                <img
                                    :src="`/storage/${slider.image}`"
                                    :alt="slider.title"
                                    class="w-40 h-24
                                           object-cover
                                           rounded-xl
                                           shadow-sm"
                                >

                            </td>


                            <!-- INFORMATIONS -->
                            <td class="p-4">

                                <div class="font-semibold text-gray-900">
                                    {{ slider.title }}
                                </div>

                                <div
                                    v-if="slider.subtitle"
                                    class="text-sm text-gray-500 mt-1"
                                >
                                    {{ slider.subtitle }}
                                </div>

                                <div
                                    v-if="slider.button_text"
                                    class="text-xs text-gray-400 mt-2"
                                >
                                    Bouton :
                                    {{ slider.button_text }}
                                </div>

                            </td>


                            <!-- POSITION -->
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


                            <!-- STATUT -->
                            <td class="p-4 text-center">

                                <button
                                    type="button"
                                    @click="toggleSlider(slider.id)"
                                    class="px-3 py-1.5
                                           rounded-full
                                           text-sm
                                           font-medium
                                           transition"
                                    :class="
                                        slider.active
                                            ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                            : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
                                    "
                                >

                                    <span
                                        class="inline-block w-2 h-2
                                               rounded-full mr-2"
                                        :class="
                                            slider.active
                                                ? 'bg-green-500'
                                                : 'bg-gray-400'
                                        "
                                    ></span>

                                    {{
                                        slider.active
                                            ? 'Active'
                                            : 'Inactive'
                                    }}

                                </button>

                            </td>


                            <!-- ACTIONS -->
                            <td class="p-4">

                                <div class="flex justify-end gap-2">

                                    <!-- MODIFIER -->
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
                                               hover:bg-blue-200
                                               transition"
                                    >
                                        Modifier
                                    </Link>


                                    <!-- SUPPRIMER -->
                                    <button
                                        type="button"
                                        @click="deleteSlider(slider.id)"
                                        class="px-4 py-2
                                               bg-red-100
                                               text-red-700
                                               rounded-lg
                                               hover:bg-red-200
                                               transition"
                                    >
                                        Supprimer
                                    </button>

                                </div>

                            </td>

                        </tr>


                        <!-- AUCUNE BANNIÈRE -->
                        <tr v-if="props.sliders.length === 0">

                            <td
                                colspan="5"
                                class="p-16 text-center"
                            >

                                <div class="text-gray-400 text-5xl mb-4">
                                    🖼️
                                </div>

                                <h3
                                    class="text-lg
                                           font-semibold
                                           text-gray-700"
                                >
                                    Aucune bannière
                                </h3>

                                <p class="text-gray-500 mt-1">
                                    Commencez par créer votre première bannière.
                                </p>

                                <Link
                                    :href="
                                        route(
                                            'admin.hero-sliders.create'
                                        )
                                    "
                                    class="inline-block
                                           mt-5
                                           bg-blue-600
                                           hover:bg-blue-700
                                           text-white
                                           px-5 py-3
                                           rounded-xl
                                           font-semibold"
                                >
                                    Créer une bannière
                                </Link>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>
</template>