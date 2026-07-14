<script setup>
import { Link } from '@inertiajs/vue3'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation, Pagination } from 'swiper'
import { Autoplay } from 'swiper/modules'

import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'swiper/css/autoplay'

defineProps({
    sliders: {
        type: Array,
        // required: true,
    },
})
</script>

<template>

    <Swiper :modules="[Navigation, Pagination, Autoplay]" :slides-per-view="1" :loop="true" :autoplay="{
        delay: 4000
    }" navigation pagination>

        <SwiperSlide v-for="slider in sliders" :key="slider.id">

            <section
                class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-blue-900 to-slate-800 text-white">

                <div class="max-w-7xl mx-auto px-8 py-20">

                    <div class="grid lg:grid-cols-2 gap-10 items-center">

                        <div>

                            <h1 class="text-6xl font-bold">

                                {{ slider.title }}

                            </h1>

                            <p class="mt-6 text-xl">

                                {{ slider.subtitle }}

                            </p>

                            <div class="mt-10">

                                <Link :href="slider.button_link" class="bg-blue-600 px-8 py-4 rounded-xl">

                                    {{ slider.button_text }}

                                </Link>

                            </div>

                        </div>

                        <div>

                            <img :src="'/storage/' + slider.image" class="rounded-2xl w-full">

                        </div>

                    </div>

                </div>

            </section>

        </SwiperSlide>

    </Swiper>

</template>