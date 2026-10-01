<script setup>
defineProps({
    label: { type: String, default: '' },
    hint: { type: String, default: '' },
    error: { type: String, default: '' },
})
</script>

<template>
    <label class="block">
        <span v-if="label" class="mb-1 block text-sm font-medium text-gray-700">{{ label }}</span>
        <slot />
        <span v-if="hint" class="mt-1 block text-xs text-gray-500">{{ hint }}</span>
        <span v-if="error" class="mt-1 block text-xs font-medium text-red-600">{{ error }}</span>
    </label>
</template>
