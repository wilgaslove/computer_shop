<script setup>
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password')
        },
    })
}
</script>

<template>
    <GuestLayout>
        <Head title="Connexion" />

        <!-- Message de statut -->
        <div
            v-if="status"
            class="mb-4 rounded-lg bg-green-50 border border-green-200
                   px-4 py-3 text-sm text-green-700"
        >
            {{ status }}
        </div>

        <!-- Erreur générale de connexion -->
        <div
            v-if="form.errors.email && !form.email"
            class="mb-4 rounded-lg bg-red-50 border border-red-200
                   px-4 py-3 text-sm text-red-700"
        >
            {{ form.errors.email }}
        </div>

        <form @submit.prevent="submit">
            <!-- EMAIL -->
            <div>
                <InputLabel
                    for="email"
                    value="Adresse email"
                />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500':
                            form.errors.email
                    }"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.email"
                />
            </div>

            <!-- PASSWORD -->
            <div class="mt-4">
                <InputLabel
                    for="password"
                    value="Mot de passe"
                />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500':
                            form.errors.password
                    }"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password"
                />
            </div>

            <!-- REMEMBER -->
            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox
                        name="remember"
                        v-model:checked="form.remember"
                    />

                    <span class="ms-2 text-sm text-gray-600">
                        Se souvenir de moi
                    </span>
                </label>
            </div>

            <!-- ACTIONS -->
            <div class="mt-6 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-gray-600 underline
                           hover:text-gray-900"
                >
                    Mot de passe oublié ?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    {{
                        form.processing
                            ? 'Connexion...'
                            : 'Se connecter'
                    }}
                </PrimaryButton>
            </div>
        </form>

        <!-- REGISTER -->
        <div class="mt-6 text-center text-sm text-gray-600">
            Vous n'avez pas encore de compte ?

            <Link
                :href="route('register')"
                class="text-blue-600 font-semibold hover:text-blue-700"
            >
                Créer un compte
            </Link>
        </div>
    </GuestLayout>
</template>