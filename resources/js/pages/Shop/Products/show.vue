<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import ShopLayout from '@/Layouts/ShopLayout.vue'

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
})

// Liste des images à afficher : galerie si elle existe, sinon l'image
// de couverture (ou rien).
const gallery = computed(() => {
    if (props.product.images?.length) {
        return props.product.images.map((img) => `/storage/${img.path}`)
    }

    if (props.product.cover_image) {
        return [`/storage/${props.product.cover_image}`]
    }

    return []
})

const activeIndex = ref(0)
const activeImage = computed(() => gallery.value[activeIndex.value] ?? null)
const imageFailed = ref(false)

function onImageError() {
    imageFailed.value = true
}

function selectImage(index) {
    activeIndex.value = index
    imageFailed.value = false
}

// Lightbox (zoom / détail de l'image)
const lightboxOpen = ref(false)

function openLightbox() {
    if (activeImage.value) lightboxOpen.value = true
}

function closeLightbox() {
    lightboxOpen.value = false
}

// Panier
const quantity = ref(1)
const adding = ref(false)

function increment() {
    if (quantity.value < props.product.stock) quantity.value++
}

function decrement() {
    if (quantity.value > 1) quantity.value--
}

function addToCart() {
    if (props.product.stock <= 0 || adding.value) return

    adding.value = true

    router.post(
        route('cart.store', props.product.id),
        { quantity: quantity.value },
        {
            preserveScroll: true,
            onFinish: () => { adding.value = false },
            onError: (errors) => { console.error('Erreur ajout panier :', errors) },
        }
    )
}
</script>

<template>
    <Head :title="$page.props.seo?.title ?? props.product.name" />

    <ShopLayout>

        <!-- Breadcrumb -->
        <section class="border-b border-gray-200 bg-white">
            <div
                class="mx-auto max-w-7xl px-6 py-4 lg:px-8"
            >
                <div class="flex min-w-0 items-center gap-2 text-sm text-gray-500">

                    <Link
                        :href="route('shop.products')"
                        class="hover:text-blue-600"
                    >
                        Boutique
                    </Link>

                    <span>
                        /
                    </span>

                    <span class="truncate text-gray-900">
                        {{ props.product.name }}
                    </span>

                </div>
            </div>
        </section>

        <!-- Produit -->
        <section class="bg-gray-50 py-8 lg:py-12">

            <div
                class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-6 lg:grid-cols-2 lg:px-8"
            >

                <!-- Galerie d'images -->
                <div>

                    <div
                        class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm"
                    >

                        <button
                            v-if="activeImage && !imageFailed"
                            type="button"
                            class="block aspect-square w-full cursor-zoom-in"
                            @click="openLightbox"
                        >
                            <img
                                :src="activeImage"
                                :alt="props.product.name"
                                class="h-full w-full object-cover"
                                @error="onImageError"
                            >
                        </button>

                        <div
                            v-else
                            class="flex aspect-square items-center justify-center"
                        >
                            <div class="text-center text-gray-400">

                                <div class="text-5xl sm:text-7xl">
                                    💻
                                </div>

                                <p class="mt-4">
                                    {{ imageFailed
                                        ? "L'image n'a pas pu être chargée (vérifiez le lien de stockage : php artisan storage:link)"
                                        : 'Aucune image disponible' }}
                                </p>

                            </div>
                        </div>

                    </div>

                    <!-- Vignettes -->
                    <div
                        v-if="gallery.length > 1"
                        class="mt-4 grid grid-cols-4 gap-2 sm:grid-cols-5 sm:gap-3"
                    >
                        <button
                            v-for="(src, index) in gallery"
                            :key="index"
                            type="button"
                            class="aspect-square overflow-hidden rounded-xl border-2 bg-white transition"
                            :class="index === activeIndex
                                ? 'border-blue-600'
                                : 'border-gray-200 hover:border-blue-300'"
                            @click="selectImage(index)"
                        >
                            <img
                                :src="src"
                                :alt="`${props.product.name} ${index + 1}`"
                                class="h-full w-full object-cover"
                            >
                        </button>
                    </div>

                </div>

                <!-- Informations -->
                <div class="flex flex-col justify-center">

                    <!-- Catégorie -->
                    <p
                        v-if="props.product.category"
                        class="text-sm font-semibold uppercase tracking-wide text-blue-600"
                    >
                        {{ props.product.category.name }}
                    </p>

                    <!-- Nom -->
                    <h1
                        class="mt-3 text-3xl font-bold tracking-tight text-gray-900 md:text-4xl"
                    >
                        {{ props.product.name }}
                    </h1>

                    <!-- Prix -->
                    <div class="mt-6 flex flex-wrap items-baseline gap-x-4 gap-y-1">

                        <span
                            class="text-3xl font-bold sm:text-4xl"
                            :class="props.product.is_on_promotion ? 'text-red-600' : 'text-blue-700'"
                        >
                            {{
                                Number(props.product.current_price)
                                    .toLocaleString('fr-FR')
                            }}
                            FCFA
                        </span>

                        <template v-if="props.product.is_on_promotion">
                            <span class="text-xl text-gray-400 line-through">
                                {{ Number(props.product.price).toLocaleString('fr-FR') }} FCFA
                            </span>

                            <span class="rounded-full bg-red-600 px-3 py-1 text-sm font-bold text-white">
                                -{{ props.product.discount_percent }}%
                            </span>
                        </template>

                    </div>

                    <!-- Stock -->
                    <div class="mt-5">

                        <span
                            v-if="props.product.stock > 0"
                            class="inline-flex items-center rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700"
                        >
                            ✓ Disponible
                        </span>

                        <span
                            v-else
                            class="inline-flex items-center rounded-full bg-red-100 px-4 py-2 text-sm font-semibold text-red-700"
                        >
                            Rupture de stock
                        </span>

                    </div>

                    <!-- Description -->
                    <div class="mt-8">

                        <h2
                            class="text-lg font-semibold text-gray-900"
                        >
                            Description
                        </h2>

                        <p
                            v-if="props.product.description"
                            class="mt-3 leading-7 text-gray-600"
                        >
                            {{ props.product.description }}
                        </p>

                        <p
                            v-else
                            class="mt-3 text-gray-500"
                        >
                            Aucune description disponible.
                        </p>

                    </div>

                    <!-- Stock -->
                    <div
                        v-if="props.product.stock > 0"
                        class="mt-8 rounded-xl border border-gray-200 bg-white p-4"
                    >
                        <p class="text-sm text-gray-500">
                            Quantité disponible
                        </p>

                        <p
                            class="mt-1 text-xl font-bold text-gray-900"
                        >
                            {{ props.product.stock }} unité(s)
                        </p>
                    </div>

                    <!-- Sélecteur de quantité -->
                    <div
                        v-if="props.product.stock > 0"
                        class="mt-6 flex items-center gap-3"
                    >
                        <span class="text-sm font-medium text-gray-700">Quantité</span>

                        <div class="flex items-center rounded-xl border border-gray-300">
                            <button
                                type="button"
                                class="px-3 py-2 text-lg font-semibold text-gray-600 hover:text-blue-600"
                                @click="decrement"
                            >
                                −
                            </button>

                            <span class="w-10 text-center font-semibold text-gray-900">
                                {{ quantity }}
                            </span>

                            <button
                                type="button"
                                class="px-3 py-2 text-lg font-semibold text-gray-600 hover:text-blue-600"
                                @click="increment"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">

                        <button
                            type="button"
                            :disabled="props.product.stock <= 0 || adding"
                            class="flex-1 rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="addToCart"
                        >
                            {{ adding ? 'Ajout...' : '🛒 Ajouter au panier' }}
                        </button>

                        <button
                            type="button"
                            class="rounded-xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-50"
                        >
                            ❤️
                        </button>

                    </div>

                    <!-- Retour -->
                    <Link
                        :href="route('shop.products')"
                        class="mt-6 inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600"
                    >
                        ← Retour à la boutique
                    </Link>

                </div>

            </div>

        </section>

        <!-- Lightbox : détail / zoom de l'image -->
        <div
            v-if="lightboxOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-6"
            @click.self="closeLightbox"
        >
            <button
                type="button"
                class="absolute right-6 top-6 rounded-full bg-white/10 p-2 text-2xl text-white hover:bg-white/20"
                @click="closeLightbox"
            >
                ✕
            </button>

            <img
                :src="activeImage"
                :alt="props.product.name"
                class="max-h-[85vh] max-w-full rounded-lg object-contain"
            >

            <div
                v-if="gallery.length > 1"
                class="absolute bottom-6 flex gap-2"
            >
                <button
                    v-for="(src, index) in gallery"
                    :key="index"
                    type="button"
                    class="h-2.5 w-2.5 rounded-full"
                    :class="index === activeIndex ? 'bg-white' : 'bg-white/40'"
                    @click="selectImage(index)"
                >
                </button>
            </div>
        </div>

    </ShopLayout>
</template>
