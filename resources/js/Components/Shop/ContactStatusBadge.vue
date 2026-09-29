<script setup>
import { computed } from 'vue'

const props = defineProps({
    status: { type: String, required: true },
    label: { type: String, default: '' },
})

const classes = {
    new:         'bg-red-100 text-red-800',
    in_progress: 'bg-amber-100 text-amber-800',
    answered:    'bg-green-100 text-green-800',
    closed:      'bg-gray-200 text-gray-700',
}

const cls = computed(() => classes[props.status] ?? 'bg-gray-100 text-gray-700')
</script>

<template>
    <span class="inline-block whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold" :class="cls">
        {{ label || status }}
    </span>
</template>
