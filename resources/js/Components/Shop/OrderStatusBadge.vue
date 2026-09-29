<script setup>
import { computed } from 'vue'

const props = defineProps({
    status: { type: String, required: true },
    label: { type: String, default: '' },
})

const classes = {
    pending:   'bg-amber-100 text-amber-800',
    confirmed: 'bg-blue-100 text-blue-800',
    shipped:   'bg-indigo-100 text-indigo-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
}

const cls = computed(() => classes[props.status] ?? 'bg-gray-100 text-gray-700')
</script>

<template>
    <span class="inline-block whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold" :class="cls">
        {{ label || status }}
    </span>
</template>
