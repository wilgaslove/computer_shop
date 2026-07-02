<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>

<section
    class="relative overflow-hidden rounded-3xl
           bg-gradient-to-r
           from-slate-900
           via-blue-900
           to-slate-800
           text-white"
>

    <div class="max-w-7xl mx-auto px-8 py-20">

        <div class="grid lg:grid-cols-2 gap-10 items-center">

            <!-- Texte -->

            <div>

                <span
                    class="inline-block
                           bg-blue-600
                           px-4
                           py-2
                           rounded-full
                           text-sm
                           font-semibold"
                >
                    🔥 Promotions de la semaine
                </span>

                <h1
                    class="text-5xl lg:text-6xl
                           font-extrabold
                           mt-6
                           leading-tight"
                >
                    Les meilleurs PC
                    <br>

                    au meilleur prix
                </h1>

                <p
                    class="mt-6
                           text-lg
                           text-gray-300
                           leading-8"
                >
                    Découvrez notre sélection de PC portables,
                    ordinateurs Gaming,
                    stations de travail et accessoires
                    informatiques au meilleur prix.
                </p>

                <div class="flex gap-4 mt-10">

                    <Link
                        :href="route('shop.products')"
                        class="bg-blue-600 hover:bg-blue-700
                               px-8 py-4 rounded-xl
                               font-semibold"
                    >
                        Acheter maintenant
                    </Link>

                    <Link
                        :href="route('shop.products')"
                        class="border border-white
                               hover:bg-white
                               hover:text-black
                               px-8 py-4
                               rounded-xl"
                    >
                        Voir les produits
                    </Link>

                </div>

            </div>

            <!-- Image -->

            <div class="flex justify-center">

                <img
                    src="/images/hero-laptop.png"
                    alt="Laptop"
                    class="w-full max-w-xl drop-shadow-2xl"
                >

            </div>

        </div>

    </div>

</section>

</template>