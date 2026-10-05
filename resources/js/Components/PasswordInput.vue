<script setup>
import { ref } from 'vue'

defineProps({
    id: {
        type: String,
        required: true,
    },
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '••••••••',
    },
    autocomplete: {
        type: String,
        default: 'current-password',
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    hasError: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue'])

const showPassword = ref(false)

const togglePassword = () => {
    showPassword.value = !showPassword.value
}
</script>

<template>
    <div class="relative">
        <input
            :id="id"
            :type="showPassword ? 'text' : 'password'"
            :value="modelValue"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            :required="required"
            :disabled="disabled"
            @input="emit('update:modelValue', $event.target.value)"
            class="w-full rounded-xl px-4 py-3 pe-12 border
                   focus:outline-none focus:ring-2
                   disabled:bg-gray-100 disabled:cursor-not-allowed"
            :class="
                hasError
                    ? 'border-red-500 focus:ring-red-200'
                    : 'border-gray-300 focus:border-blue-500 focus:ring-blue-100'
            "
        />

        <button
            type="button"
            @click="togglePassword"
            :disabled="disabled"
            class="absolute inset-y-0 end-0 flex items-center
                   px-3 text-gray-500 hover:text-gray-700
                   focus:outline-none disabled:cursor-not-allowed"
            :aria-label="
                showPassword
                    ? 'Masquer le mot de passe'
                    : 'Afficher le mot de passe'
            "
        >
            <!-- Œil ouvert -->
            <svg
                v-if="showPassword"
                xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                />
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                />
            </svg>

            <!-- Œil fermé -->
            <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3.98 8.223A10.477 10.477 0 002.458 12C3.732 16.057 7.523 19 12 19c.782 0 1.54-.09 2.258-.26M6.228 6.228A10.45 10.45 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.5 10.5 0 01-4.132 5.411M6.228 6.228L3 3m3.228 3.228l12.544 12.544M9.88 9.88a3 3 0 104.24 4.24"
                />
            </svg>
        </button>
    </div>
</template>