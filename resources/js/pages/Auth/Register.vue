<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// formulaire d'inscription
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <!-- <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nom Prénom" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required    
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Mot de passe" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirmez le mot de passe"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Déjà inscrit?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    S'INSCRIRE
                </PrimaryButton>
            </div>
        </form> -->

         <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

            <h1 class="text-3xl font-bold text-center mb-2">
                Créer un compte
            </h1>

            <p class="text-gray-500 text-center mb-8">
                Créez votre compte pour continuer
            </p>

            <form @submit.prevent="submit" class="space-y-5">

                <!-- Nom -->
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Nom
                    </label>

                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full border rounded-xl px-4 py-3"
                        placeholder="Votre nom"
                    >

                    <div
                        v-if="form.errors.name"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.name }}
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Email
                    </label>

                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full border rounded-xl px-4 py-3"
                        placeholder="vous@example.com"
                    >

                    <div
                        v-if="form.errors.email"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.email }}
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Mot de passe
                    </label>

                    <input
                        v-model="form.password"
                        type="password"
                        class="w-full border rounded-xl px-4 py-3"
                        placeholder="••••••••"
                    >

                    <div
                        v-if="form.errors.password"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.password }}
                    </div>
                </div>

                <!-- Confirmation -->
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Confirmer le mot de passe
                    </label>

                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        class="w-full border rounded-xl px-4 py-3"
                        placeholder="••••••••"
                    >
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-blue-600 hover:bg-blue-700
                           text-white py-3 rounded-xl font-semibold
                           disabled:opacity-50"
                >
                    {{ form.processing ? 'Création...' : 'Créer mon compte' }}
                </button>

            </form>

            <div class="text-center mt-6 text-sm">
                Déjà un compte ?

                <Link
                    :href="route('login')"
                    class="text-blue-600 font-semibold"
                >
                    Se connecter
                </Link>
            </div>

        </div>
    </div>
    </GuestLayout>
</template>

