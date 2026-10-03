<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const page = usePage()

const user = computed(() => page.props?.auth?.user ?? null)
const cartCount = computed(() => page.props?.cart?.count ?? 0)
const isStaff = computed(() => page.props?.auth?.is_staff ?? false)
const newMessages = computed(() => page.props?.admin?.new_messages ?? 0)
const brand = computed(() => page.props?.site?.brand ?? 'LogInova Tech')

const menuOpen = ref(false)    // menu du compte (avatar)
const mobileOpen = ref(false)  // menu burger (mobile / tablette)

// Liens du menu : un seul endroit pour la barre desktop ET le menu mobile
const menuLinks = computed(() => [
    { label: 'Accueil', href: route('home'), active: route().current('home') },
    { label: 'Produits', href: route('shop.products'), active: route().current('shop.products') && !currentQuery() },
    { label: 'HP', href: route('shop.products', { q: 'HP' }) },
    { label: 'Dell', href: route('shop.products', { q: 'Dell' }) },
    { label: 'Lenovo', href: route('shop.products', { q: 'Lenovo' }) },
    { label: 'Asus', href: route('shop.products', { q: 'Asus' }) },
    { label: '🔥 Promotions', href: route('shop.promotions'), active: route().current('shop.promotions'), promo: true },
    { label: 'Contact', href: route('contact'), active: route().current('contact') },
])

// Recherche : la valeur reflète toujours le paramètre ?q= de l'URL courante
const searchQuery = ref('')

function currentQuery() {
    try {
        return new URL(page.url, 'http://local').searchParams.get('q') ?? ''
    } catch {
        return ''
    }
}

// À chaque navigation : on aligne la recherche et on referme les menus
watch(() => page.url, () => {
    searchQuery.value = currentQuery()
    mobileOpen.value = false
    menuOpen.value = false
}, { immediate: true })

function search() {
    const q = searchQuery.value.trim()

    mobileOpen.value = false
    router.get(route('shop.products'), q ? { q } : {})
}

function linkClass(link) {
    if (link.promo) return 'font-semibold text-red-600 hover:text-red-700'

    return link.active ? 'font-semibold text-blue-600' : 'hover:text-blue-600'
}
</script>

<template>
    <header class="sticky top-0 z-50">

        <!-- ================= Barre principale ================= -->
        <div class="bg-slate-900 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">

                <div class="flex h-16 items-center gap-3 sm:gap-4 lg:h-20 lg:gap-6">

                    <!-- Burger (mobile + tablette) -->
                    <button
                        type="button"
                        class="-ml-2 flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg text-2xl hover:bg-slate-800 lg:hidden"
                        :aria-expanded="mobileOpen"
                        aria-controls="mobile-menu"
                        aria-label="Menu"
                        @click="mobileOpen = !mobileOpen"
                    >
                        {{ mobileOpen ? '✕' : '☰' }}
                    </button>

                    <!-- Logo -->
                    <Link
                        :href="route('home')"
                        class="flex flex-shrink-0 items-center"
                        :aria-label="brand"
                    >
                        <img
                            src="/images/logo-navbar.png"
                            :alt="brand"
                            class="h-10 w-auto sm:h-12 lg:h-14"
                        >
                    </Link>

                    <!-- Recherche (desktop) -->
                    <form class="hidden flex-1 lg:block" role="search" @submit.prevent="search">
                        <div class="flex">
                            <input
                                v-model="searchQuery"
                                type="search"
                                maxlength="100"
                                class="w-full rounded-l-lg px-4 py-3 text-black outline-none"
                                placeholder="Rechercher un ordinateur..."
                            >
                            <button type="submit" class="rounded-r-lg bg-blue-600 px-6 hover:bg-blue-700" aria-label="Rechercher">
                                🔍
                            </button>
                        </div>
                    </form>

                    <!-- Actions à droite -->
                    <div class="ml-auto flex items-center gap-2 sm:gap-4 lg:ml-0">

                        <!-- Favoris -->
                        <button type="button" class="hidden text-xl hover:text-blue-400 sm:block" aria-label="Favoris">
                            ❤️
                        </button>

                        <!-- Panier -->
                        <Link
                            :href="route('cart.index')"
                            class="relative flex h-11 w-11 items-center justify-center text-xl hover:text-blue-400"
                            aria-label="Panier"
                        >
                            🛒
                            <span
                                v-if="cartCount"
                                class="absolute right-0 top-1 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-red-500 px-1 text-xs"
                            >
                                {{ cartCount }}
                            </span>
                        </Link>

                        <!-- Utilisateur connecté -->
                        <div v-if="user" class="relative">
                            <button
                                type="button"
                                class="flex items-center gap-3"
                                :aria-expanded="menuOpen"
                                aria-label="Mon compte"
                                @click="menuOpen = !menuOpen"
                            >
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 font-bold">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>

                                <div class="hidden text-left xl:block">
                                    <div class="text-sm">Bonjour</div>
                                    <div class="max-w-[10rem] truncate font-semibold">{{ user.name }} ▾</div>
                                </div>
                            </button>

                            <!-- Fermeture au clic à l'extérieur -->
                            <div v-if="menuOpen" class="fixed inset-0 z-40" @click="menuOpen = false"></div>

                            <div
                                v-if="menuOpen"
                                class="absolute right-0 z-50 mt-3 w-56 overflow-hidden rounded-xl bg-white py-1 text-sm text-gray-800 shadow-lg"
                                @click="menuOpen = false"
                            >
                                <p class="truncate border-b border-gray-100 px-4 py-2 text-xs text-gray-500">{{ user.name }}</p>

                                <Link v-if="isStaff" :href="route('admin.dashboard')" class="block px-4 py-3 hover:bg-gray-100">
                                    ⚙️ Administration
                                </Link>

                                <Link :href="route('account.orders.index')" class="block px-4 py-3 hover:bg-gray-100">
                                    📦 Mes commandes
                                </Link>

                                <Link :href="route('profile.edit')" class="block px-4 py-3 hover:bg-gray-100">
                                    👤 Mon profil
                                </Link>

                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="block w-full border-t border-gray-100 px-4 py-3 text-left text-red-600 hover:bg-red-50"
                                >
                                    🚪 Déconnexion
                                </Link>
                            </div>
                        </div>

                        <!-- Non connecté -->
                        <div v-else class="flex items-center gap-2 text-sm sm:gap-3 sm:text-base">
                            <Link :href="route('login')" class="whitespace-nowrap hover:text-blue-400">
                                Connexion
                            </Link>

                            <Link
                                :href="route('register')"
                                class="hidden whitespace-nowrap rounded-lg bg-blue-600 px-4 py-2 hover:bg-blue-700 sm:block"
                            >
                                Inscription
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recherche (mobile + tablette) -->
                <form class="pb-3 lg:hidden" role="search" @submit.prevent="search">
                    <div class="flex">
                        <input
                            v-model="searchQuery"
                            type="search"
                            maxlength="100"
                            class="w-full min-w-0 rounded-l-lg px-4 py-2.5 text-black outline-none"
                            placeholder="Rechercher un ordinateur..."
                        >
                        <button type="submit" class="rounded-r-lg bg-blue-600 px-5 hover:bg-blue-700" aria-label="Rechercher">
                            🔍
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- ================= Menu desktop ================= -->
        <div class="hidden bg-white shadow lg:block">
            <div class="mx-auto max-w-7xl">
                <nav class="flex h-14 items-center gap-8 px-6" aria-label="Navigation principale">
                    <Link
                        v-for="link in menuLinks"
                        :key="link.label"
                        :href="link.href"
                        :class="linkClass(link)"
                    >
                        {{ link.label }}
                    </Link>

                    <!-- Accès direct à l'administration (équipe uniquement) -->
                    <Link
                        v-if="isStaff"
                        :href="route('admin.dashboard')"
                        class="ml-auto flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-1.5 text-sm font-semibold text-white transition hover:bg-slate-700"
                    >
                        ⚙️ Administration
                        <span
                            v-if="newMessages"
                            class="rounded-full bg-red-500 px-2 py-0.5 text-xs font-bold"
                            :title="`${newMessages} nouveau(x) message(s)`"
                        >
                            {{ newMessages }}
                        </span>
                    </Link>
                </nav>
            </div>
        </div>

        <!-- ================= Menu mobile (burger) ================= -->
        <div
            v-if="mobileOpen"
            id="mobile-menu"
            class="max-h-[calc(100vh-7.5rem)] overflow-y-auto bg-white text-gray-800 shadow-lg lg:hidden"
        >
            <nav class="mx-auto max-w-7xl divide-y divide-gray-100 px-4 sm:px-6" aria-label="Navigation mobile">
                <Link
                    v-for="link in menuLinks"
                    :key="link.label"
                    :href="link.href"
                    class="block py-3.5 text-base"
                    :class="linkClass(link)"
                >
                    {{ link.label }}
                </Link>

                <Link
                    v-if="isStaff"
                    :href="route('admin.dashboard')"
                    class="flex items-center gap-2 py-3.5 text-base font-semibold"
                >
                    ⚙️ Administration
                    <span v-if="newMessages" class="rounded-full bg-red-500 px-2 py-0.5 text-xs font-bold text-white">
                        {{ newMessages }}
                    </span>
                </Link>

                <!-- Visiteur : l'inscription (masquée dans la barre sur très petit écran) -->
                <Link
                    v-if="!user"
                    :href="route('register')"
                    class="block py-3.5 text-base font-semibold text-blue-600"
                >
                    Créer un compte
                </Link>
            </nav>
        </div>

    </header>
</template>
