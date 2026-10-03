<script setup>
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
    sliders: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>

    <!--
        Aucun slider actif :
        on n'affiche rien.
    -->
    <section
        v-if="props.sliders.length > 0"
        class="w-full py-6 sm:py-8 lg:py-10"
    >

        <Swiper
            :modules="[Autoplay, Navigation, Pagination]"
            :slides-per-view="1"
            :loop="props.sliders.length > 1"
            :autoplay="{
                delay: 5000,
                disableOnInteraction: false,
            }"
            :navigation="props.sliders.length > 1"
            :pagination="{
                clickable: true,
            }"
            class="hero-swiper"
        >

            <SwiperSlide
                v-for="slider in props.sliders"
                :key="slider.id"
            >

                <!-- CADRE DU HERO -->
                <div
                    class="
                        relative
                        h-[300px]
                        sm:h-[380px]
                        md:h-[460px]
                        lg:h-[540px]
                        overflow-hidden
                        rounded-2xl
                        bg-gray-900
                    "
                >

                    <!-- IMAGE -->
                   <div
        class="
            absolute
            inset-0
            flex
            items-center
            justify-center
            p-4
            sm:p-6
            lg:p-8
        "
    >
        <img
            :src="`/storage/${slider.image}`"
            :alt="slider.title"
            class="
                max-w-full
                max-h-full
                w-auto
                h-auto
                object-contain
            "
        />
    </div>

                    <!-- OVERLAY -->
                    <div
                        class="
                            absolute
                            inset-0
                            bg-black/40
                        "
                    ></div>

                    <!-- CONTENU -->
                    <div
                        class="
                            relative
                            z-10
                            h-full
                            max-w-7xl
                            mx-auto
                            px-6
                            sm:px-10
                            lg:px-14
                            flex
                            items-center
                        "
                    >

                        <div
                            class="
                                max-w-2xl
                                text-white
                            "
                        >

                            <!-- TITRE -->
                            <h1
                                class="
                                    text-3xl
                                    sm:text-4xl
                                    md:text-5xl
                                    lg:text-6xl
                                    font-bold
                                    leading-tight
                                "
                            >
                                {{ slider.title }}
                            </h1>

                            <!-- SOUS-TITRE -->
                            <p
                                v-if="slider.subtitle"
                                class="
                                    mt-4
                                    sm:mt-5
                                    text-base
                                    sm:text-lg
                                    md:text-xl
                                    text-gray-100
                                    max-w-xl
                                "
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
                                class="
                                    inline-flex
                                    items-center
                                    mt-6
                                    sm:mt-8
                                    px-5
                                    sm:px-6
                                    py-3
                                    rounded-xl
                                    bg-blue-600
                                    hover:bg-blue-700
                                    text-white
                                    font-semibold
                                    transition
                                "
                            >
                                {{ slider.button_text }}

                                <span class="ml-2">
                                    →
                                </span>
                            </a>

                        </div>

                    </div>

                </div>

            </SwiperSlide>

        </Swiper>

    </section>

</template>

<style scoped>

.hero-swiper {
    width: 80%;
    margin: 0 auto;
}


/*
|--------------------------------------------------------------------------
| Navigation
|--------------------------------------------------------------------------
*/

.hero-swiper :deep(.swiper-button-next),
.hero-swiper :deep(.swiper-button-prev) {
    width: 38px;
    height: 38px;

    border-radius: 9999px;

    /* background: ; */

    transition: all 0.2s ease;
}

.hero-swiper :deep(.swiper-button-next:hover),
.hero-swiper :deep(.swiper-button-prev:hover) {
    background: white;
}

.hero-swiper :deep(.swiper-button-next::after),
.hero-swiper :deep(.swiper-button-prev::after) {
    font-size: 16px;
    font-weight: 700;
}


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

.hero-swiper :deep(.swiper-pagination) {
    bottom: 18px;
}

.hero-swiper :deep(.swiper-pagination-bullet) {
    width: 9px;
    height: 9px;
    opacity: 0.8;
}

.hero-swiper :deep(.swiper-pagination-bullet-active) {
    width: 24px;
    border-radius: 9999px;
}


/*
|--------------------------------------------------------------------------
| Responsive
|--------------------------------------------------------------------------
*/

@media (max-width: 639px) {

    .hero-swiper {
        width: 95%;
    }

    .hero-swiper :deep(.swiper-button-next),
    .hero-swiper :deep(.swiper-button-prev) {
        display: none;
    }
}

</style>