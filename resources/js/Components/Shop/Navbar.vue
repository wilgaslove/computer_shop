<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const page = usePage()

const user = computed(() => page.props?.auth?.user ?? null)
const cartCount = computed(() => page.props?.cart?.count ?? 0)
const isStaff = computed(() => page.props?.auth?.is_staff ?? false)
const menuOpen = ref(false)

// Recherche : la valeur reflète toujours le paramètre ?q= de l'URL courante
const searchQuery = ref('')

function currentQuery() {
    try {
        return new URL(page.url, 'http://local').searchParams.get('q') ?? ''
    } catch {
        return ''
    }
}

watch(() => page.url, () => { searchQuery.value = currentQuery() }, { immediate: true })

function search() {
    const q = searchQuery.value.trim()

    router.get(route('shop.products'), q ? { q } : {})
}

</script>

<template>

    <header class="sticky top-0 z-50">

        <!-- Barre principale -->

        <div class="bg-slate-900 text-white">

            <div class="max-w-7xl mx-auto px-6">

                <div class="flex items-center h-20 gap-6">

                    <!-- Logo -->

                    <Link :href="route('shop.products.index')" class="text-3xl font-extrabold whitespace-nowrap">
                        💻 ComputerShop
                    </Link>

                    <!-- Recherche -->

                    <form class="flex-1" role="search" @submit.prevent="search">

                        <div class="flex">

                            <input v-model="searchQuery" type="search" maxlength="100"
                                class="w-full px-4 py-3 rounded-l-lg text-black outline-none"
                                placeholder="Rechercher un ordinateur...">

                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-6 rounded-r-lg" aria-label="Rechercher">
                                🔍
                            </button>

                        </div>

                    </form>

                    <!-- Favoris -->

                    <button class="text-xl hover:text-blue-400">
                        ❤️
                    </button>

                    <!-- Panier -->

                    <Link :href="route('cart.index')" class="relative text-xl hover:text-blue-400">

                        🛒

                        <span
                            class="absolute -top-2 -right-2 bg-red-500 text-xs h-5 w-5 rounded-full flex items-center justify-center">
                            {{ cartCount }}
                        </span>

                    </Link>

                    <!-- Utilisateur -->

                    <div v-if="user" class="relative">

                        <button type="button" class="flex items-center gap-3" @click="menuOpen = !menuOpen">

                            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </div>

                            <div class="text-left">
                                <div class="text-sm">Bonjour</div>
                                <div class="font-semibold">{{ user.name }} ▾</div>
                            </div>

                        </button>

                        <!-- Fermeture au clic à l'extérieur -->
                        <div v-if="menuOpen" class="fixed inset-0 z-40" @click="menuOpen = false"></div>

                        <div
                            v-if="menuOpen"
                            class="absolute right-0 z-50 mt-3 w-56 overflow-hidden rounded-xl bg-white py-1 text-sm text-gray-800 shadow-lg"
                            @click="menuOpen = false"
                        >
                            <Link v-if="isStaff" :href="route('admin.dashboard')" class="block px-4 py-2 hover:bg-gray-100">
                                ⚙️ Administration
                            </Link>

                            <Link :href="route('account.orders.index')" class="block px-4 py-2 hover:bg-gray-100">
                                📦 Mes commandes
                            </Link>

                            <Link :href="route('profile.edit')" class="block px-4 py-2 hover:bg-gray-100">
                                👤 Mon profil
                            </Link>

                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="block w-full border-t border-gray-100 px-4 py-2 text-left text-red-600 hover:bg-red-50"
                            >
                                🚪 Déconnexion
                            </Link>
                        </div>

                    </div>

                    <!-- Non connecté -->

                    <div v-else class="flex gap-3">

                        <Link :href="route('login')" class="hover:text-blue-400">
                            Connexion
                        </Link>

                        <Link :href="route('register')" class="bg-blue-600 px-4 py-2 rounded-lg hover:bg-blue-700">
                            Inscription
                        </Link>

                    </div>

                </div>

            </div>

        </div>

        <!-- Menu -->

        <div class="bg-white shadow">

            <div class="max-w-7xl mx-auto">

                <nav class="flex gap-8 h-14 items-center px-6">

                    <Link :href="route('shop.products.index')">
                        Accueil
                    </Link>

                    <Link :href="route('shop.products')">
                        Produits
                    </Link>

                    <Link :href="route('shop.products', { q: 'HP' })">
                        HP
                    </Link>

                    <Link :href="route('shop.products', { q: 'Dell' })">
                        Dell
                    </Link>

                    <Link :href="route('shop.products', { q: 'Lenovo' })">
                        Lenovo
                    </Link>

                    <Link :href="route('shop.products', { q: 'Asus' })">
                        Asus
                    </Link>

                    <a href="#">
                        Promotions
                    </a>

                    <a href="#">
                        Contact
                    </a>

                </nav>

            </div>

        </div>

    </header>

</template>