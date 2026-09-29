<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Toast from '@/Components/Shop/Toast.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const isAdmin = computed(() => page.props.auth?.is_admin ?? false)
const menuOpen = ref(false)

const links = computed(() => [
    { label: 'Tableau de bord', icon: '📊', href: route('admin.dashboard'), active: route().current('admin.dashboard') },
    { label: 'Commandes', icon: '🧾', href: route('admin.orders.index'), active: route().current('admin.orders.*') },
    { label: 'Produits', icon: '💻', href: route('admin.products.index'), active: route().current('admin.products.*') },
    { label: 'Catégories', icon: '🗂️', href: route('admin.categories.index'), active: route().current('admin.categories.*') },
    { label: 'Bannières', icon: '🖼️', href: route('admin.hero-sliders.index'), active: route().current('admin.hero-sliders.*') },
    ...(isAdmin.value
        ? [{ label: 'Utilisateurs', icon: '👥', href: route('admin.users.index'), active: route().current('admin.users.*') }]
        : []),
])
</script>

<template>
    <div class="min-h-screen bg-gray-100 lg:flex">

        <!-- Barre mobile -->
        <div class="flex items-center justify-between bg-slate-900 px-4 py-3 text-white lg:hidden">
            <span class="font-bold">💻 ComputerShop · Admin</span>
            <button type="button" class="text-2xl" @click="menuOpen = !menuOpen">☰</button>
        </div>

        <!-- Menu latéral -->
        <aside
            class="w-full flex-shrink-0 bg-slate-900 text-white lg:block lg:min-h-screen lg:w-64"
            :class="menuOpen ? 'block' : 'hidden'"
        >
            <div class="hidden px-6 py-6 text-xl font-extrabold lg:block">
                💻 ComputerShop
                <span class="block text-xs font-normal text-slate-400">Administration</span>
            </div>

            <nav class="space-y-1 px-3 pb-4 lg:pb-0">
                <Link
                    v-for="link in links"
                    :key="link.label"
                    :href="link.href"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition"
                    :class="link.active ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800'"
                    @click="menuOpen = false"
                >
                    <span>{{ link.icon }}</span>
                    {{ link.label }}
                </Link>

                <div class="my-3 border-t border-slate-700" />

                <Link
                    :href="route('shop.products.index')"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm text-slate-300 hover:bg-slate-800"
                >
                    <span>🛍️</span> Voir la boutique
                </Link>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-sm text-red-300 hover:bg-slate-800"
                >
                    <span>🚪</span> Déconnexion
                </Link>
            </nav>

            <p v-if="user" class="mt-6 hidden px-6 text-xs text-slate-500 lg:block">
                Connecté : {{ user.name }}
            </p>
        </aside>

        <!-- Contenu -->
        <main class="min-w-0 flex-1 p-4 sm:p-8">
            <slot />
        </main>

        <Toast />
    </div>
</template>
