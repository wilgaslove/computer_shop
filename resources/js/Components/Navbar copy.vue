<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.value?.auth?.user ?? null)

</script>

<template>

    <header class="sticky top-0 z-50">

        <!-- Barre principale -->
        <div class="flex items-center gap-5">

            <button>
                ❤️
            </button>

            <button class="relative">

                🛒

                <span class="absolute -top-2 -right-2
                   bg-red-500
                   text-white
                   rounded-full
                   w-5
                   h-5
                   text-xs
                   flex
                   items-center
                   justify-center">
                    0
                </span>

            </button>

            <!-- Utilisateur connecté -->

            <template v-if="user">

                <div class="relative group">

                    <button class="font-semibold hover:text-blue-400">
                        {{ user.name }} ▼
                    </button>

                    <div class="absolute right-0 mt-2
                       w-56
                       bg-white
                       text-black
                       rounded-lg
                       shadow-xl
                       hidden
                       group-hover:block">

                        <Link href="/profile" class="block px-4 py-3 hover:bg-gray-100">
                            Mon profil
                        </Link>

                        <Link href="#" class="block px-4 py-3 hover:bg-gray-100">
                            Mes commandes
                        </Link>

                        <Link v-if="user.roles.includes('admin')" :href="route('admin.dashboard')"
                            class="block px-4 py-3 hover:bg-gray-100">
                            Dashboard Admin
                        </Link>

                        <Link method="post" :href="route('logout')" as="button"
                            class="w-full text-left px-4 py-3 hover:bg-gray-100">
                            Déconnexion
                        </Link>

                    </div>

                </div>

            </template>

            <!-- Visiteur -->

            <template v-else>

                <Link :href="route('login')">
                    Connexion
                </Link>

                <Link :href="route('register')" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Inscription
                </Link>

            </template>

        </div>

        <!-- Barre de navigation -->

        <div class="bg-white shadow">

            <div class="max-w-7xl mx-auto">

                <nav class="h-14 flex items-center gap-8 px-5">

                    <Link :href="route('home')" class="hover:text-blue-600">
                        Accueil
                    </Link>

                    <Link :href="route('shop.products')" class="hover:text-blue-600">
                        Produits
                    </Link>

                    <a href="#">
                        PC Portables
                    </a>

                    <a href="#">
                        Gaming
                    </a>

                    <a href="#">
                        Accessoires
                    </a>

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