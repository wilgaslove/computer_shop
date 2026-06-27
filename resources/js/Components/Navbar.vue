<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <header class="bg-white shadow-sm border-b">

        <div class="max-w-7xl mx-auto px-4">

            <div class="h-16 flex items-center justify-between">

                <!-- Logo -->
                <Link
                    :href="route('home')"
                    class="text-2xl font-bold text-blue-600"
                >
                    ComputerShop
                </Link>

                <!-- Menu -->
                <nav class="hidden md:flex items-center gap-8">

                    <Link
                        :href="route('home')"
                        class="hover:text-blue-600"
                    >
                        Accueil
                    </Link>

                    <Link
                        :href="route('shop.products')"
                        class="hover:text-blue-600"
                    >
                        Produits
                    </Link>

                    <a href="#" class="hover:text-blue-600">
                        Contact
                    </a>

                </nav>

                <!-- Actions -->
                <div class="flex items-center gap-4">

                    <button>
                        🔍
                    </button>

                    <button>
                        ❤️
                    </button>

                    <button>
                        🛒
                    </button>

                </div>

            </div>

        </div>

    </header>
</template>