<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    password: '',
})

const submit = () => {
    form.post('/register', {
        onError: (e) => console.log(e),
    })
}
</script>

<template>
<GuestLayout>
    <form @submit.prevent="submit">

        <input v-model="form.name" placeholder="Name" />
        <div v-if="form.errors.name">{{ form.errors.name }}</div>

        <input v-model="form.email" type="email" placeholder="Email" />
        <div v-if="form.errors.email">{{ form.errors.email }}</div>

        <input v-model="form.password" type="password" placeholder="Password" />
        <div v-if="form.errors.password">{{ form.errors.password }}</div>

        <button :disabled="form.processing">
            Register
        </button>

    </form>
</GuestLayout>
</template>