<script setup>
import { usePage } from '@inertiajs/vue3'
import { watch, ref } from 'vue'

const page = usePage()
const message = ref(null)
const type = ref('success')

function show(text, kind) {
    message.value = text
    type.value = kind
    setTimeout(() => { message.value = null }, 3000)
}

watch(
    () => page.props.flash?.success,
    (value) => { if (value) show(value, 'success') }
)

watch(
    () => page.props.flash?.error,
    (value) => { if (value) show(value, 'error') }
)
</script>

<template>
    <transition name="fade">
        <div
            v-if="message"
            class="fixed bottom-6 right-6 z-[100] rounded-xl px-5 py-3 font-semibold text-white shadow-lg"
            :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'"
        >
            {{ message }}
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
