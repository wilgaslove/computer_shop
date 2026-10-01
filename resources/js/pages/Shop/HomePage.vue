<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import Hero from '@/Components/Shop/Hero.vue'
import ProductCard from '@/Components/Shop/ProductCard.vue'
import SmartLink from '@/Components/Shop/SmartLink.vue'
import ShopLayout from '@/Layouts/ShopLayout.vue'

const props = defineProps({
    content: { type: Object, required: true },
    sliders: { type: Array, default: () => [] },
    newProducts: { type: Array, default: () => [] },
    promoProducts: { type: Array, default: () => [] },
    popularProducts: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
})

const c = computed(() => props.content)

// Blocs produits : affichés seulement s'ils ont des produits
const productBlocks = computed(() => [
    {
        key: 'new',
        title: c.value.products.new_title,
        subtitle: c.value.products.new_subtitle,
        items: props.newProducts,
        more: { label: 'Voir tout le catalogue', href: route('shop.products') },
    },
    {
        key: 'promo',
        title: c.value.products.promo_title,
        subtitle: c.value.products.promo_subtitle,
        items: props.promoProducts,
        more: { label: 'Toutes les promotions', href: route('shop.promotions') },
    },
    {
        key: 'popular',
        title: c.value.products.popular_title,
        subtitle: c.value.products.popular_subtitle,
        items: props.popularProducts,
        more: null,
    },
].filter((block) => block.items.length > 0))

function stars(rating) {
    const n = Math.max(0, Math.min(5, Number(rating) || 0))
    return '★'.repeat(n) + '☆'.repeat(5 - n)
}

// Newsletter
const newsletter = useForm({ email: '' })

function subscribe() {
    newsletter.post(route('newsletter.subscribe'), {
        preserveScroll: true,
        onSuccess: () => newsletter.reset('email'),
    })
}
</script>

<template>
    <Head title="Accueil" />

    <ShopLayout>
        <div class="space-y-16">

            <!-- ============ Bannière d'accueil ============ -->
            <section>
                <h1 v-if="c.hero.title" class="sr-only">{{ c.hero.title }}</h1>

                <Hero v-if="props.sliders.length" :sliders="props.sliders" />

                <div
                    v-else
                    class="overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-blue-900 px-8 py-16 text-white sm:px-14 sm:py-24"
                >
                    <p v-if="c.hero.title" class="max-w-3xl text-4xl font-extrabold leading-tight sm:text-5xl">
                        {{ c.hero.title }}
                    </p>
                    <p v-if="c.hero.subtitle" class="mt-5 max-w-2xl text-lg text-slate-300">{{ c.hero.subtitle }}</p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <SmartLink
                            v-if="c.hero.primary_label"
                            :href="c.hero.primary_link"
                            class="rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-500"
                        >
                            {{ c.hero.primary_label }}
                        </SmartLink>

                        <SmartLink
                            v-if="c.hero.secondary_label"
                            :href="c.hero.secondary_link"
                            class="rounded-xl border border-white/30 px-6 py-3 font-semibold text-white transition hover:bg-white/10"
                        >
                            {{ c.hero.secondary_label }}
                        </SmartLink>
                    </div>
                </div>
            </section>

            <!-- ============ Ce que nous vendons ============ -->
            <section v-if="c.intro.enabled" id="ce-que-nous-vendons" class="scroll-mt-40">
                <div class="mb-8 text-center">
                    <h2 class="text-3xl font-bold text-gray-900">{{ c.intro.title }}</h2>
                    <p v-if="c.intro.subtitle" class="mx-auto mt-2 max-w-2xl text-gray-600">{{ c.intro.subtitle }}</p>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <component
                        :is="item.link ? SmartLink : 'div'"
                        v-for="(item, i) in c.intro.items"
                        :key="i"
                        :href="item.link || undefined"
                        class="group rounded-2xl border border-gray-200 bg-white p-6 transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md"
                    >
                        <span class="block text-4xl">{{ item.icon }}</span>
                        <span class="mt-4 block text-lg font-bold text-gray-900 group-hover:text-blue-700">{{ item.title }}</span>
                        <span v-if="item.text" class="mt-1 block text-sm text-gray-600">{{ item.text }}</span>
                    </component>
                </div>

                <!-- Catégories réelles de la boutique -->
                <div v-if="props.categories.length" class="mt-8 flex flex-wrap items-center justify-center gap-2">
                    <span class="text-sm text-gray-500">Parcourir par catégorie :</span>
                    <Link
                        v-for="category in props.categories"
                        :key="category.id"
                        :href="route('shop.products', { category: category.id })"
                        class="rounded-full border border-gray-300 bg-white px-4 py-1.5 text-sm font-medium text-gray-700 transition hover:border-blue-500 hover:text-blue-700"
                    >
                        {{ category.name }}
                        <span class="text-gray-400">({{ category.products_count }})</span>
                    </Link>
                </div>
            </section>

            <!-- ============ Complétez votre setup ============ -->
            <section v-if="c.accessories.enabled && c.accessories.items.length" id="accessoires" class="scroll-mt-40">
                <div class="mb-8 text-center">
                    <h2 class="text-3xl font-bold text-gray-900">{{ c.accessories.title }}</h2>
                    <p v-if="c.accessories.subtitle" class="mx-auto mt-2 max-w-2xl text-gray-600">{{ c.accessories.subtitle }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    <component
                        :is="item.link ? SmartLink : 'div'"
                        v-for="(item, i) in c.accessories.items"
                        :key="i"
                        :href="item.link || undefined"
                        class="flex flex-col items-center rounded-2xl border border-gray-200 bg-white px-4 py-6 text-center transition hover:border-blue-300 hover:shadow-md"
                    >
                        <span class="text-4xl">{{ item.icon }}</span>
                        <span class="mt-3 font-semibold text-gray-800">{{ item.label }}</span>
                    </component>
                </div>
            </section>

            <!-- ============ Produits à découvrir ============ -->
            <section v-for="block in productBlocks" :key="block.key">
                <div class="mb-6 flex flex-wrap items-end justify-between gap-2">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">{{ block.title }}</h2>
                        <p v-if="block.subtitle" class="mt-1 text-gray-600">{{ block.subtitle }}</p>
                    </div>

                    <Link v-if="block.more" :href="block.more.href" class="text-sm font-semibold text-blue-600 hover:underline">
                        {{ block.more.label }} →
                    </Link>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <ProductCard v-for="product in block.items" :key="product.id" :product="product" />
                </div>
            </section>

            <!-- ============ Bannière spéciale ============ -->
            <section
                v-if="c.banner.enabled"
                class="rounded-3xl bg-blue-600 px-8 py-14 text-center text-white sm:px-14"
            >
                <h2 class="mx-auto max-w-2xl text-3xl font-extrabold sm:text-4xl">{{ c.banner.title }}</h2>
                <p v-if="c.banner.text" class="mx-auto mt-3 max-w-xl text-lg text-blue-100">{{ c.banner.text }}</p>

                <SmartLink
                    v-if="c.banner.button_label"
                    :href="c.banner.button_link"
                    class="mt-8 inline-block rounded-xl bg-white px-8 py-3 font-semibold text-blue-700 transition hover:bg-blue-50"
                >
                    {{ c.banner.button_label }}
                </SmartLink>
            </section>

            <!-- ============ Pourquoi acheter chez nous ============ -->
            <section v-if="c.why.enabled && c.why.items.length" id="pourquoi-nous" class="scroll-mt-40">
                <div class="mb-8 text-center">
                    <h2 class="text-3xl font-bold text-gray-900">{{ c.why.title }}</h2>
                    <p v-if="c.why.subtitle" class="mx-auto mt-2 max-w-2xl text-gray-600">{{ c.why.subtitle }}</p>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="(item, i) in c.why.items"
                        :key="i"
                        class="flex gap-4 rounded-2xl border border-gray-200 bg-white p-6"
                    >
                        <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 text-2xl">
                            {{ item.icon }}
                        </span>
                        <div>
                            <h3 class="font-bold text-gray-900">{{ item.title }}</h3>
                            <p v-if="item.text" class="mt-1 text-sm text-gray-600">{{ item.text }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ============ Avis clients ============ -->
            <section v-if="c.reviews.enabled && c.reviews.items.length">
                <h2 class="mb-8 text-center text-3xl font-bold text-gray-900">⭐ {{ c.reviews.title }}</h2>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    <figure
                        v-for="(review, i) in c.reviews.items"
                        :key="i"
                        class="rounded-2xl border border-gray-200 bg-white p-6"
                    >
                        <p class="text-xl tracking-wider text-amber-500" :aria-label="`${review.rating} étoiles sur 5`">
                            {{ stars(review.rating) }}
                        </p>
                        <blockquote class="mt-3 text-gray-700">« {{ review.text }} »</blockquote>
                        <figcaption class="mt-4 text-sm font-semibold text-gray-900">— {{ review.author }}</figcaption>
                    </figure>
                </div>
            </section>

            <!-- ============ FAQ ============ -->
            <section v-if="c.faq.enabled && c.faq.items.length" id="faq" class="mx-auto w-full max-w-3xl scroll-mt-40">
                <h2 class="mb-6 text-center text-3xl font-bold text-gray-900">{{ c.faq.title }}</h2>

                <div class="divide-y divide-gray-200 rounded-2xl border border-gray-200 bg-white">
                    <details v-for="(item, i) in c.faq.items" :key="i" class="group p-5">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-gray-900 [&::-webkit-details-marker]:hidden">
                            {{ item.q }}
                            <span class="text-2xl leading-none text-blue-600 transition-transform group-open:rotate-45" aria-hidden="true">+</span>
                        </summary>
                        <p class="mt-3 whitespace-pre-line text-gray-600">{{ item.a }}</p>
                    </details>
                </div>
            </section>

            <!-- ============ Newsletter ============ -->
            <section v-if="c.newsletter.enabled" class="rounded-3xl bg-slate-900 px-8 py-12 text-center text-white sm:px-14">
                <h2 class="text-2xl font-bold sm:text-3xl">{{ c.newsletter.title }}</h2>
                <p v-if="c.newsletter.text" class="mx-auto mt-2 max-w-xl text-slate-300">{{ c.newsletter.text }}</p>

                <form class="mx-auto mt-6 flex max-w-xl flex-col gap-3 sm:flex-row" novalidate @submit.prevent="subscribe">
                    <label for="newsletter-email" class="sr-only">Votre adresse e-mail</label>
                    <input
                        id="newsletter-email"
                        v-model="newsletter.email"
                        type="email"
                        autocomplete="email"
                        placeholder="Votre adresse e-mail"
                        class="w-full rounded-xl border-0 px-4 py-3 text-gray-900 focus:ring-2 focus:ring-blue-500"
                    >
                    <button
                        type="submit"
                        class="whitespace-nowrap rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-500 disabled:opacity-60"
                        :disabled="newsletter.processing"
                    >
                        {{ newsletter.processing ? 'Inscription…' : (c.newsletter.button_label || "S'inscrire") }}
                    </button>
                </form>

                <p v-if="newsletter.errors.email" class="mt-3 text-sm text-red-300">{{ newsletter.errors.email }}</p>
            </section>

        </div>
    </ShopLayout>
</template>
