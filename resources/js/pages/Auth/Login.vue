<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post('/login', {
        onError: (e) => console.log(e),
    })
}
</script>

<template>
<GuestLayout>
    <form @submit.prevent="submit">

        <input v-model="form.email" type="email" placeholder="Email" />
        <div v-if="form.errors.email">{{ form.errors.email }}</div>

        <input v-model="form.password" type="password" placeholder="Password" />
        <div v-if="form.errors.password">{{ form.errors.password }}</div>

        <label>
            <input type="checkbox" v-model="form.remember" />
            Remember me
        </label>

        <button :disabled="form.processing">
            Login
        </button>

    </form>
</GuestLayout>
</template>