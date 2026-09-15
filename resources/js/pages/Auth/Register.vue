<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation')
        },
    })
}
</script>

<template>
    <Head title="Inscription" />

    <div
        class="min-h-screen flex items-center justify-center
               bg-gray-100 px-4"
    >
        <div
            class="w-full max-w-md bg-white rounded-2xl
                   shadow-lg p-8"
        >

            <!-- TITRE -->
            <h1 class="text-3xl font-bold text-center mb-2">
                Créer un compte
            </h1>

            <p class="text-gray-500 text-center mb-8">
                Créez votre compte pour continuer
            </p>

            <form
                @submit.prevent="submit"
                class="space-y-5"
            >

                <!-- NOM -->
                <div>
                    <label
                        for="name"
                        class="block text-sm font-medium mb-2"
                    >
                        Nom
                    </label>

                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        autocomplete="name"
                        placeholder="Votre nom"
                        class="w-full rounded-xl px-4 py-3 border
                               focus:outline-none focus:ring-2"
                        :class="
                            form.errors.name
                                ? 'border-red-500 focus:ring-red-200'
                                : 'border-gray-300 focus:border-blue-500 focus:ring-blue-100'
                        "
                    >

                    <div
                        v-if="form.errors.name"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.name }}
                    </div>
                </div>

                <!-- EMAIL -->
                <div>
                    <label
                        for="email"
                        class="block text-sm font-medium mb-2"
                    >
                        Adresse email
                    </label>

                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        placeholder="vous@example.com"
                        class="w-full rounded-xl px-4 py-3 border
                               focus:outline-none focus:ring-2"
                        :class="
                            form.errors.email
                                ? 'border-red-500 focus:ring-red-200'
                                : 'border-gray-300 focus:border-blue-500 focus:ring-blue-100'
                        "
                    >

                    <div
                        v-if="form.errors.email"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.email }}
                    </div>
                </div>

                <!-- PASSWORD -->
                <div>
                    <label
                        for="password"
                        class="block text-sm font-medium mb-2"
                    >
                        Mot de passe
                    </label>

                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        autocomplete="new-password"
                        placeholder="••••••••"
                        class="w-full rounded-xl px-4 py-3 border
                               focus:outline-none focus:ring-2"
                        :class="
                            form.errors.password
                                ? 'border-red-500 focus:ring-red-200'
                                : 'border-gray-300 focus:border-blue-500 focus:ring-blue-100'
                        "
                    >

                    <div
                        v-if="form.errors.password"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.password }}
                    </div>
                </div>

                <!-- CONFIRMATION -->
                <div>
                    <label
                        for="password_confirmation"
                        class="block text-sm font-medium mb-2"
                    >
                        Confirmer le mot de passe
                    </label>

                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        placeholder="••••••••"
                        class="w-full rounded-xl px-4 py-3 border
                               focus:outline-none focus:ring-2"
                        :class="
                            form.errors.password_confirmation
                                ? 'border-red-500 focus:ring-red-200'
                                : 'border-gray-300 focus:border-blue-500 focus:ring-blue-100'
                        "
                    >

                    <div
                        v-if="form.errors.password_confirmation"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.password_confirmation }}
                    </div>
                </div>

                <!-- BOUTON -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-blue-600 hover:bg-blue-700
                           text-white py-3 rounded-xl
                           font-semibold transition
                           disabled:opacity-50
                           disabled:cursor-not-allowed"
                >
                    {{
                        form.processing
                            ? 'Création du compte...'
                            : 'Créer mon compte'
                    }}
                </button>

            </form>

            <!-- LOGIN -->
            <div class="text-center mt-6 text-sm text-gray-600">
                Vous avez déjà un compte ?

                <Link
                    :href="route('login')"
                    class="text-blue-600 font-semibold hover:text-blue-700"
                >
                    Se connecter
                </Link>
            </div>

        </div>
    </div>
</template>