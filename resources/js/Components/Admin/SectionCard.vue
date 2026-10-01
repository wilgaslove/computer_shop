<script setup>
defineProps({
    title: { type: String, required: true },
    description: { type: String, default: '' },
    // Si fourni (v-model:enabled), affiche la case « Afficher sur le site »
    enabled: { type: Boolean, default: undefined },
})

const emit = defineEmits(['update:enabled'])
</script>

<template>
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <header class="mb-5 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ title }}</h2>
                <p v-if="description" class="mt-0.5 text-sm text-gray-500">{{ description }}</p>
            </div>

            <label v-if="enabled !== undefined" class="flex cursor-pointer items-center gap-2 text-sm font-medium text-gray-700">
                <input
                    type="checkbox"
                    :checked="enabled"
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    @change="emit('update:enabled', $event.target.checked)"
                >
                Afficher sur le site
            </label>
        </header>

        <div class="space-y-4" :class="enabled === false ? 'opacity-60' : ''">
            <slot />
        </div>
    </section>
</template>
