<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()

const user = computed(() => page.props?.auth?.user ?? null)
</script>

<template>

    <header class="sticky top-0 z-50">

        <!-- Barre principale -->

        <div class="bg-slate-900 text-white">

            <div class="max-w-7xl mx-auto px-6">

                <div class="flex items-center h-20 gap-6">

                    <!-- Logo -->

                    <Link :href="route('home')" class="text-3xl font-extrabold whitespace-nowrap">
                        💻 ComputerShop
                    </Link>

                    <!-- Recherche -->

                    <div class="flex-1">

                        <div class="flex">

                            <input class="w-full px-4 py-3 rounded-l-lg text-black outline-none"
                                placeholder="Rechercher un ordinateur...">

                            <button class="bg-blue-600 hover:bg-blue-700 px-6 rounded-r-lg">
                                🔍
                            </button>

                        </div>

                    </div>

                    <!-- Favoris -->

                    <button class="text-xl hover:text-blue-400">
                        ❤️
                    </button>

                    <!-- Panier -->

                    <button class="relative text-xl hover:text-blue-400">

                        🛒

                        <span
                            class="absolute -top-2 -right-2 bg-red-500 text-xs h-5 w-5 rounded-full flex items-center justify-center">
                            0
                        </span>

                    </button>

                    <!-- Utilisateur -->

                    <div v-if="user">

                        <div class="flex items-center gap-3">

                            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </div>

                            <div>

                                <div class="text-sm">
                                    Bonjour
                                </div>

                                <div class="font-semibold">
                                    {{ user.name }}
                                </div>

                            </div>

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

                    <Link :href="route('home')">
                        Accueil
                    </Link>

                    <Link :href="route('shop.products')">
                        Produits
                    </Link>

                    <!-- <Link :href="route('admin.hero-sliders.index')" class="flex items-center gap-2">
                        🖼 Hero Slider
                    </Link> -->

                    <a href="#">
                        HP
                    </a>

                    <a href="#">
                        Dell
                    </a>

                    <a href="#">
                        Lenovo
                    </a>

                    <a href="#">
                        Asus
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