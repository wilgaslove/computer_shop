<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>

<header class="sticky top-0 z-50">

    <!-- Barre principale -->
    <div class="bg-slate-900 text-white">

        <div class="max-w-7xl mx-auto px-5">

            <div class="h-20 flex items-center gap-6">

                <!-- Logo -->

                <Link
                    :href="route('home')"
                    class="text-3xl font-extrabold whitespace-nowrap"
                >
                    💻 ComputerShop
                </Link>

                <!-- Recherche -->

                <div class="flex-1">

                    <div class="flex">

                        <input
                            type="text"
                            placeholder="Rechercher un ordinateur..."
                            class="w-full rounded-l-lg px-4 py-3 text-black outline-none"
                        >

                        <button
                            class="bg-blue-600 hover:bg-blue-700 px-5 rounded-r-lg"
                        >
                            🔍
                        </button>

                    </div>

                </div>

                <!-- Favoris -->

                <button
                    class="hover:text-blue-400 transition"
                >
                    ❤️
                </button>

                <!-- Panier -->

                <button
                    class="relative hover:text-blue-400 transition"
                >

                    🛒

                    <span
                        class="absolute -top-2 -right-3 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
                    >
                        0
                    </span>

                </button>

                <!-- Compte -->

                <Link
                    :href="route('login')"
                    class="hover:text-blue-400"
                >
                    👤
                </Link>

            </div>

        </div>

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