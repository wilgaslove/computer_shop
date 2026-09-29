<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import OrderStatusBadge from '@/Components/Shop/OrderStatusBadge.vue'

const props = defineProps({
    contact: { type: Object, required: true },
    orders: { type: Array, default: () => [] },
    selectedOrder: { type: String, default: null },
    defaults: { type: Object, default: () => ({ name: '', email: '' }) },
})

const page = usePage()
const isLoggedIn = computed(() => !!page.props.auth?.user)

/* ---------------- Coordonnées ---------------- */

const phoneHref = computed(() => 'tel:' + props.contact.phone.replace(/[^\d+]/g, ''))
const whatsappHref = computed(() => 'https://wa.me/' + props.contact.whatsapp.replace(/\D/g, ''))
const emailHref = computed(() => 'mailto:' + props.contact.email)

const quickContacts = computed(() => [
    { icon: '📞', label: 'Téléphone', value: props.contact.phone, href: phoneHref.value, external: false },
    { icon: '💬', label: 'WhatsApp', value: props.contact.whatsapp, href: whatsappHref.value, external: true },
    { icon: '📧', label: 'Email', value: props.contact.email, href: emailHref.value, external: false },
])

/* ---------------- Formulaire ---------------- */

const AUTO_SUBJECT = 'Problème avec la commande '

const form = useForm({
    name: props.defaults.name ?? '',
    email: props.defaults.email ?? '',
    phone: '',
    subject: props.selectedOrder ? AUTO_SUBJECT + props.selectedOrder : '',
    message: '',
    order: props.selectedOrder ?? '',
    attachment: null,
})

const fileKey = ref(0) // permet de vider le champ fichier après l'envoi
const sent = ref(false)

// La confirmation disparaît dès que le visiteur commence un nouveau message.
watch(() => form.message, (value) => {
    if (value) sent.value = false
})

function selectOrder(order) {
    form.order = order.reference
    form.clearErrors('order')

    // On ne remplace pas un sujet que le client a écrit lui-même.
    if (!form.subject || form.subject.startsWith(AUTO_SUBJECT)) {
        form.subject = AUTO_SUBJECT + order.reference
    }

    document.getElementById('formulaire')?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function clearOrder() {
    if (form.subject.startsWith(AUTO_SUBJECT)) form.subject = ''
    form.order = ''
}

const orderBeingDiscussed = computed(() => props.orders.find((o) => o.reference === form.order) ?? null)

function submit() {
    form.post(route('contact.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.subject = ''
            form.message = ''
            form.order = ''
            form.attachment = null
            fileKey.value++
            sent.value = true
        },
    })
}

/* ---------------- FAQ (textes à adapter à votre boutique) ---------------- */

const faqs = [
    {
        q: 'Comment suivre ma commande ?',
        a: 'Connectez-vous, puis ouvrez « Mes commandes » : le statut (en attente, confirmée, expédiée, livrée) est mis à jour à chaque étape.',
    },
    {
        q: 'Comment retourner un produit ?',
        a: 'Écrivez-nous via le formulaire ci-dessus en indiquant votre numéro de commande et le motif du retour. Nous vous indiquons la marche à suivre.',
    },
    {
        q: 'Quels sont les délais de livraison ?',
        a: 'Ils dépendent de votre ville et de la disponibilité du produit. Le statut « Expédiée » indique que votre commande est en route ; pour un délai précis, contactez-nous.',
    },
    {
        q: 'Quelle est votre politique de garantie ?',
        a: 'La durée de garantie dépend du produit et de la marque. Conservez la référence de votre commande comme preuve d\'achat et contactez-nous pour toute demande de prise en charge.',
    },
]

/* ---------------- Carte (OpenStreetMap, sans clé API) ---------------- */

const mapSrc = computed(() => {
    const { map_lat: lat, map_lng: lng } = props.contact
    const dLng = 0.02
    const dLat = 0.012

    return 'https://www.openstreetmap.org/export/embed.html'
        + `?bbox=${lng - dLng}%2C${lat - dLat}%2C${lng + dLng}%2C${lat + dLat}`
        + `&layer=mapnik&marker=${lat}%2C${lng}`
})

const mapLink = computed(() => {
    const { map_lat: lat, map_lng: lng } = props.contact
    return `https://www.openstreetmap.org/?mlat=${lat}&mlon=${lng}#map=15/${lat}/${lng}`
})

const inputClass = 'w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500'
</script>

<template>
    <Head title="Contact" />

    <ShopLayout>
        <!-- En-tête -->
        <header class="mx-auto max-w-2xl text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Contactez-nous</h1>
            <p class="mt-3 text-gray-600">Une question ? Notre équipe est là pour vous aider.</p>
        </header>

        <!-- Contacts rapides -->
        <section class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3" aria-label="Contacts rapides">
            <a
                v-for="item in quickContacts"
                :key="item.label"
                :href="item.href"
                :target="item.external ? '_blank' : undefined"
                :rel="item.external ? 'noopener noreferrer' : undefined"
                class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-md"
            >
                <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 text-2xl">
                    {{ item.icon }}
                </span>
                <span class="min-w-0">
                    <span class="block text-sm text-gray-500">{{ item.label }}</span>
                    <span class="block truncate font-semibold text-gray-900">{{ item.value }}</span>
                </span>
            </a>
        </section>

        <!-- Problème avec une commande (client connecté) -->
        <section v-if="isLoggedIn && props.orders.length" class="mt-10 rounded-2xl border border-blue-100 bg-blue-50 p-6">
            <h2 class="text-lg font-bold text-gray-900">Vous avez un problème avec une commande ?</h2>
            <p class="mt-1 text-sm text-gray-600">
                Choisissez-la : votre message sera automatiquement rattaché à cette commande.
            </p>

            <ul class="mt-4 space-y-3">
                <li
                    v-for="order in props.orders"
                    :key="order.reference"
                    class="flex flex-col gap-3 rounded-xl bg-white p-4 sm:flex-row sm:items-center sm:justify-between"
                    :class="form.order === order.reference ? 'ring-2 ring-blue-500' : ''"
                >
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900">
                            Commande #{{ order.reference }}
                            <OrderStatusBadge :status="order.status" :label="order.status_label" class="ml-2 align-middle" />
                        </p>
                        <p class="truncate text-sm text-gray-500">{{ order.summary }}</p>
                    </div>

                    <button
                        type="button"
                        class="whitespace-nowrap rounded-xl px-4 py-2 text-sm font-semibold transition"
                        :class="form.order === order.reference
                            ? 'bg-blue-600 text-white'
                            : 'border border-blue-600 text-blue-600 hover:bg-blue-50'"
                        @click="selectOrder(order)"
                    >
                        {{ form.order === order.reference ? '✓ Sélectionnée' : 'Contacter le service client' }}
                    </button>
                </li>
            </ul>
        </section>

        <p v-else-if="!isLoggedIn" class="mt-6 text-center text-sm text-gray-500">
            Un problème avec une commande ?
            <Link :href="route('login')" class="font-medium text-blue-600 hover:underline">Connectez-vous</Link>
            pour rattacher votre message à celle-ci.
        </p>

        <!-- Formulaire + informations -->
        <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-5">

            <!-- Formulaire -->
            <section id="formulaire" class="scroll-mt-40 rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 lg:col-span-3">
                <h2 class="text-xl font-bold text-gray-900">Envoyez-nous un message</h2>

                <div
                    v-if="sent"
                    role="status"
                    class="mt-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800"
                >
                    ✓ Message envoyé. Notre équipe vous répondra par e-mail dès que possible.
                </div>

                <form class="mt-6 space-y-5" novalidate @submit.prevent="submit">

                    <!-- Commande concernée -->
                    <div
                        v-if="form.order"
                        class="flex items-center justify-between gap-3 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm"
                    >
                        <span class="text-gray-700">
                            Commande concernée :
                            <strong class="text-gray-900">#{{ form.order }}</strong>
                            <span v-if="orderBeingDiscussed" class="text-gray-500"> · {{ orderBeingDiscussed.summary }}</span>
                        </span>
                        <button type="button" class="font-medium text-blue-600 hover:underline" @click="clearOrder">
                            Retirer
                        </button>
                    </div>
                    <p v-if="form.errors.order" class="text-sm text-red-600">{{ form.errors.order }}</p>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label for="c-name" class="mb-1 block text-sm font-medium text-gray-700">Nom</label>
                            <input id="c-name" v-model="form.name" type="text" autocomplete="name" :class="inputClass">
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label for="c-email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                            <input id="c-email" v-model="form.email" type="email" autocomplete="email" :class="inputClass">
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>
                    </div>

                    <div>
                        <label for="c-phone" class="mb-1 block text-sm font-medium text-gray-700">
                            Téléphone <span class="font-normal text-gray-400">(facultatif)</span>
                        </label>
                        <input id="c-phone" v-model="form.phone" type="tel" autocomplete="tel" :class="inputClass">
                        <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                    </div>

                    <div>
                        <label for="c-subject" class="mb-1 block text-sm font-medium text-gray-700">Sujet</label>
                        <input id="c-subject" v-model="form.subject" type="text" maxlength="190" :class="inputClass">
                        <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">{{ form.errors.subject }}</p>
                    </div>

                    <div>
                        <label for="c-message" class="mb-1 block text-sm font-medium text-gray-700">Message</label>
                        <textarea id="c-message" v-model="form.message" rows="6" maxlength="5000" :class="inputClass" />
                        <p v-if="form.errors.message" class="mt-1 text-sm text-red-600">{{ form.errors.message }}</p>
                    </div>

                    <div>
                        <label for="c-file" class="mb-1 block text-sm font-medium text-gray-700">
                            Pièce jointe <span class="font-normal text-gray-400">(facultatif)</span>
                        </label>
                        <input
                            id="c-file"
                            :key="fileKey"
                            type="file"
                            accept=".jpg,.jpeg,.png,.webp,.pdf"
                            class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:font-medium file:text-gray-700 hover:file:bg-gray-200"
                            @change="form.attachment = $event.target.files[0] ?? null"
                        >
                        <p class="mt-1 text-xs text-gray-500">Photo ou PDF, 4 Mo maximum (ex : facture, capture d'écran).</p>
                        <p v-if="form.errors.attachment" class="mt-1 text-sm text-red-600">{{ form.errors.attachment }}</p>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Envoi en cours…' : 'Envoyer le message' }}
                    </button>
                </form>
            </section>

            <!-- Informations -->
            <aside class="h-fit rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 lg:col-span-2">
                <h2 class="text-xl font-bold text-gray-900">Nos informations</h2>

                <dl class="mt-6 space-y-5 text-sm">
                    <div class="flex gap-4">
                        <dt class="text-xl" aria-hidden="true">📍</dt>
                        <div>
                            <dd class="text-gray-500">Adresse</dd>
                            <dd class="font-medium text-gray-900">{{ props.contact.address }}</dd>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <dt class="text-xl" aria-hidden="true">📞</dt>
                        <div>
                            <dd class="text-gray-500">Téléphone</dd>
                            <dd><a :href="phoneHref" class="font-medium text-gray-900 hover:text-blue-600">{{ props.contact.phone }}</a></dd>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <dt class="text-xl" aria-hidden="true">📧</dt>
                        <div class="min-w-0">
                            <dd class="text-gray-500">Email</dd>
                            <dd><a :href="emailHref" class="break-all font-medium text-gray-900 hover:text-blue-600">{{ props.contact.email }}</a></dd>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <dt class="text-xl" aria-hidden="true">🕐</dt>
                        <div>
                            <dd class="text-gray-500">Horaires</dd>
                            <dd class="font-medium text-gray-900">{{ props.contact.hours }}</dd>
                        </div>
                    </div>
                </dl>
            </aside>
        </div>

        <!-- Questions fréquentes -->
        <section class="mx-auto mt-14 max-w-3xl">
            <h2 class="text-2xl font-bold text-gray-900">Questions fréquentes</h2>

            <div class="mt-6 divide-y divide-gray-200 rounded-2xl border border-gray-200 bg-white">
                <details v-for="item in faqs" :key="item.q" class="group p-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-gray-900 [&::-webkit-details-marker]:hidden">
                        {{ item.q }}
                        <span class="text-2xl leading-none text-blue-600 transition-transform group-open:rotate-45" aria-hidden="true">+</span>
                    </summary>
                    <p class="mt-3 text-gray-600">{{ item.a }}</p>
                </details>
            </div>
        </section>

        <!-- Nous trouver -->
        <section class="mt-14">
            <div class="flex flex-wrap items-end justify-between gap-2">
                <h2 class="text-2xl font-bold text-gray-900">Nous trouver</h2>
                <p class="text-gray-600">📍 {{ props.contact.address }}</p>
            </div>

            <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <iframe
                    :src="mapSrc"
                    title="Carte de notre localisation"
                    class="h-80 w-full border-0"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                />
            </div>

            <a
                :href="mapLink"
                target="_blank"
                rel="noopener noreferrer"
                class="mt-3 inline-block text-sm font-medium text-blue-600 hover:underline"
            >
                Agrandir la carte
            </a>
        </section>
    </ShopLayout>
</template>
