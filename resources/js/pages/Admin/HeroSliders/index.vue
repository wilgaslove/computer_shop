<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    sliders: Array
})
</script>

<template>

<div class="p-8">

    <div class="flex justify-between items-center mb-8">

        <h1 class="text-3xl font-bold">
            Hero Slider
        </h1>

        <Link
            :href="route('admin.hero-sliders.create')"
            class="bg-blue-600 text-white px-4 py-2 rounded"
        >
            + Nouvelle bannière
        </Link>

    </div>

    <table class="w-full border">

        <thead>

        <tr class="bg-gray-100">

            <th class="p-3">Image</th>

            <th>Titre</th>

            <th>Ordre</th>

            <th>Active</th>

            <th>Actions</th>

        </tr>

        </thead>

        <tbody>

        <tr
            v-for="slider in sliders"
            :key="slider.id"
        >

            <td class="p-2">

                <img
                    :src="'/storage/' + slider.image"
                    class="w-32 rounded"
                >

            </td>

            <td>{{ slider.title }}</td>

            <td>{{ slider.position }}</td>

            <td>

                <span
                    v-if="slider.active"
                    class="text-green-600"
                >
                    Active
                </span>

                <span
                    v-else
                    class="text-red-600"
                >
                    Désactivée
                </span>

            </td>

            <td>

                Modifier | Supprimer

            </td>

        </tr>

        </tbody>

    </table>

</div>

</template>