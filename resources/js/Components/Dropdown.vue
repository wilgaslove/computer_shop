<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: Array,
        default: () => ['py-1', 'bg-white'],
    },
})

const showing = ref(false)

function closeOnEscape(event) {
    if (event.key === 'Escape') {
        showing.value = false
    }
}

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape)
})

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape)
})
</script>

<template>
    <div class="relative">

        <!-- Trigger -->
        <div @click="showing = !showing">
            <slot name="trigger" />
        </div>

        <!-- Dropdown -->
        <div
            v-show="showing"
            class="absolute z-50 mt-2 rounded-md shadow-lg"
            :class="[
                width === '48' ? 'w-48' : '',
                align === 'right'
                    ? 'right-0'
                    : 'left-0'
            ]"
            @click="showing = false"
        >
            <div
                class="rounded-md ring-1 ring-black ring-opacity-5"
                :class="contentClasses"
            >
                <slot name="content" />
            </div>
        </div>

    </div>
</template>