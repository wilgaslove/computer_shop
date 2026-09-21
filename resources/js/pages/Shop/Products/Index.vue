<script setup>
import ProductCard from '@/Components/Shop/ProductCard.vue'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import Hero from '@/Components/Shop/Hero.vue'


import { Head } from '@inertiajs/vue3'

import { Swiper, SwiperSlide } from 'swiper/vue'
import {
    Autoplay,
    Navigation,
    Pagination,
} from 'swiper/modules'

import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },

    heroSliders: {
        type: Array,
        default: () => [],
    },
})


defineProps({
    products: {
        type: Object,
        // sliders: Array,
        required: true, 
    },
    sliders: {
        type: Array,
        required: true,
    }
})
</script>
<template>
    <Head title="Boutique" />

    <ShopLayout>
<div>

        <!-- HERO SLIDER -->
        <section
            v-if="props.heroSliders.length"
            class="relative"
        >

            <Swiper
                :modules="[Autoplay, Navigation, Pagination]"
                :slides-per-view="1"
                :loop="props.heroSliders.length > 1"
                :autoplay="{
                    delay: 5000,
                    disableOnInteraction: false,
                }"
                :navigation="props.heroSliders.length > 1"
                :pagination="{
                    clickable: true,
                }"
                class="hero-swiper"
            >

                <SwiperSlide
                    v-for="slider in props.heroSliders"
                    :key="slider.id"
                >

                    <div
                        class="relative
                               h-[420px]
                               md:h-[520px]
                               overflow-hidden
                               bg-gray-900"
                    >

                        <!-- IMAGE -->
                        <img
                            :src="`/storage/${slider.image}`"
                            :alt="slider.title"
                            class="absolute inset-0
                                   w-full h-full
                                   object-cover"
                        >

                        <!-- OVERLAY -->
                        <div
                            class="absolute inset-0
                                   bg-black/45"
                        ></div>


                        <!-- CONTENU -->
                        <div
                            class="relative z-10
                                   h-full
                                   max-w-7xl
                                   mx-auto
                                   px-6
                                   flex
                                   items-center"
                        >

                            <div
                                class="max-w-2xl
                                       text-white"
                            >

                                <!-- TITRE -->
                                <h1
                                    class="text-4xl
                                           md:text-5xl
                                           lg:text-6xl
                                           font-bold
                                           leading-tight"
                                >
                                    {{ slider.title }}
                                </h1>


                                <!-- SOUS-TITRE -->
                                <p
                                    v-if="slider.subtitle"
                                    class="mt-5
                                           text-lg
                                           md:text-xl
                                           text-gray-100"
                                >
                                    {{ slider.subtitle }}
                                </p>


                                <!-- BOUTON -->
                                <a
                                    v-if="
                                        slider.button_text &&
                                        slider.button_link
                                    "
                                    :href="slider.button_link"
                                    class="inline-flex
                                           mt-8
                                           px-6
                                           py-3
                                           rounded-xl
                                           bg-blue-600
                                           hover:bg-blue-700
                                           text-white
                                           font-semibold
                                           transition"
                                >
                                    {{ slider.button_text }}
                                </a>

                            </div>

                        </div>

                    </div>

                </SwiperSlide>

            </Swiper>

        </section>


        <!-- PRODUITS -->
        <section class="max-w-7xl mx-auto px-6 py-12">

            <div class="mb-8">

                <h2
                    class="text-3xl
                           font-bold
                           text-gray-900"
                >
                    Nos produits
                </h2>

                <p class="text-gray-500 mt-2">
                    Découvrez nos ordinateurs et équipements.
                </p>

            </div>

            <!-- TON CODE PRODUITS EXISTANT -->
            <!-- garde ici ta grille actuelle -->

        </section>

    </div>
    </ShopLayout>
</template>
