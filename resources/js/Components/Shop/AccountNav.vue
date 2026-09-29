<script setup>
import { Link } from '@inertiajs/vue3'

const links = [
    { label: 'Mes commandes', icon: '📦', href: () => route('account.orders.index'), active: () => route().current('account.orders.*') },
    { label: 'Mon profil', icon: '👤', href: () => route('profile.edit'), active: () => route().current('profile.*') },
]
</script>

<template>
    <nav class="flex gap-2 overflow-x-auto rounded-2xl border border-gray-200 bg-white p-2 lg:flex-col">
        <Link
            v-for="link in links"
            :key="link.label"
            :href="link.href()"
            class="flex items-center gap-3 whitespace-nowrap rounded-xl px-4 py-3 text-sm font-medium transition"
            :class="link.active()
                ? 'bg-blue-600 text-white'
                : 'text-gray-700 hover:bg-gray-100'"
        >
            <span>{{ link.icon }}</span>
            {{ link.label }}
        </Link>

        <Link
            :href="route('logout')"
            method="post"
            as="button"
            class="flex items-center gap-3 whitespace-nowrap rounded-xl px-4 py-3 text-left text-sm font-medium text-red-600 transition hover:bg-red-50"
        >
            <span>🚪</span>
            Déconnexion
        </Link>
    </nav>
</template>
