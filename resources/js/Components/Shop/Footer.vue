<script setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import SmartLink from '@/Components/Shop/SmartLink.vue'

const page = usePage()

// Contenu modifiable depuis Admin > Accueil du site > Coordonnées & pied de page
const site = computed(() => page.props?.site ?? {})
const columns = computed(() => site.value.footer_columns ?? [])

const phoneHref = computed(() => 'tel:' + (site.value.phone ?? '').replace(/[^\d+]/g, ''))
const whatsappHref = computed(() => 'https://wa.me/' + (site.value.whatsapp ?? '').replace(/\D/g, ''))

const socials = computed(() => [
    { label: 'Facebook', href: site.value.facebook },
    { label: 'Instagram', href: site.value.instagram },
    { label: 'WhatsApp', href: site.value.whatsapp ? whatsappHref.value : null },
].filter((s) => s.href))
</script>

<template>
    <footer class="mt-16 bg-slate-900 text-slate-300">
        <div class="mx-auto max-w-7xl px-6 py-12">

            <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-5">

                <!-- Marque + coordonnées -->
                <div class="lg:col-span-2">
                    <p class="text-2xl font-extrabold text-white">💻 {{ site.brand }}</p>
                    <p v-if="site.tagline" class="mt-2 text-slate-400">{{ site.tagline }}</p>

                    <ul class="mt-6 space-y-2 text-sm">
                        <li v-if="site.address">📍 {{ site.address }}</li>
                        <li v-if="site.phone">
                            📞 <a :href="phoneHref" class="hover:text-white">{{ site.phone }}</a>
                        </li>
                        <li v-if="site.email">
                            📧 <a :href="'mailto:' + site.email" class="break-all hover:text-white">{{ site.email }}</a>
                        </li>
                        <li v-if="site.hours">🕐 {{ site.hours }}</li>
                    </ul>

                    <div v-if="socials.length" class="mt-6 flex flex-wrap gap-3">
                        <a
                            v-for="social in socials"
                            :key="social.label"
                            :href="social.href"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="rounded-full border border-slate-600 px-4 py-1.5 text-sm transition hover:border-white hover:text-white"
                        >
                            {{ social.label }}
                        </a>
                    </div>
                </div>

                <!-- Colonnes de liens -->
                <nav v-for="(column, i) in columns" :key="i" :aria-label="column.title">
                    <h3 class="font-semibold text-white">{{ column.title }}</h3>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li v-for="(link, j) in column.links" :key="j">
                            <SmartLink :href="link.url" class="transition hover:text-white">{{ link.label }}</SmartLink>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="mt-10 border-t border-slate-700 pt-6 text-sm text-slate-400">
                © {{ new Date().getFullYear() }} {{ site.brand }} — Tous droits réservés
            </div>
        </div>
    </footer>
</template>
