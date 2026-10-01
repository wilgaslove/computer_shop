<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import OrderStatusBadge from '@/Components/Shop/OrderStatusBadge.vue'
import ContactStatusBadge from '@/Components/Shop/ContactStatusBadge.vue'

const props = defineProps({
    stats: { type: Object, required: true },
    recentOrders: { type: Array, default: () => [] },
    recentMessages: { type: Array, default: () => [] },
    lowStock: { type: Array, default: () => [] },
})

const page = usePage()
const isAdmin = computed(() => page.props.auth?.is_admin ?? false)
const canCreateProduct = computed(() => page.props.auth?.can?.product?.create ?? false)

// Cartes de chiffres : celles qui ont un href sont cliquables et mènent à la section correspondante.
const cards = computed(() => [
    { label: 'Ventes (hors annulées)', value: formatFcfa(props.stats.sales), color: 'text-blue-700' },
    { label: 'Encaissé', value: formatFcfa(props.stats.collected), color: 'text-green-700' },
    {
        label: 'Commandes en attente',
        value: props.stats.pending_orders,
        color: 'text-amber-600',
        href: route('admin.orders.index', { status: 'pending' }),
    },
    {
        label: 'Messages à traiter',
        value: props.stats.new_messages,
        color: props.stats.new_messages > 0 ? 'text-red-600' : 'text-gray-900',
        href: route('admin.contact-messages.index', { status: 'new' }),
        highlight: props.stats.new_messages > 0,
    },
    { label: 'Commandes au total', value: props.stats.orders, color: 'text-gray-900', href: route('admin.orders.index') },
    { label: 'Produits', value: props.stats.products, color: 'text-gray-900', href: route('admin.products.index') },
    { label: 'Catégories', value: props.stats.categories, color: 'text-gray-900', href: route('admin.categories.index') },
    // La liste des utilisateurs est réservée aux administrateurs
    { label: 'Clients', value: props.stats.customers, color: 'text-gray-900', href: isAdmin.value ? route('admin.users.index') : null },
])

// Accès rapides
const shortcuts = computed(() => [
    { icon: '✉️', label: 'Messages de contact', href: route('admin.contact-messages.index') },
    { icon: '🧾', label: 'Toutes les commandes', href: route('admin.orders.index') },
    { icon: '💻', label: 'Gérer les produits', href: route('admin.products.index') },
    ...(canCreateProduct.value ? [{ icon: '➕', label: 'Ajouter un produit', href: route('admin.products.create') }] : []),
    { icon: '🏠', label: "Modifier la page d'accueil", href: route('admin.site-content.edit') },
    { icon: '🖼️', label: 'Bannières', href: route('admin.hero-sliders.index') },
    { icon: '📬', label: 'Inscrits newsletter', href: route('admin.newsletter.index') },
    { icon: '🔥', label: 'Voir les promotions (boutique)', href: route('shop.promotions') },
])

const statusLabels = {
    pending: 'En attente',
    confirmed: 'Confirmée',
    shipped: 'Expédiée',
    delivered: 'Livrée',
    cancelled: 'Annulée',
}

function formatFcfa(value) {
    return `${Number(value).toLocaleString('fr-FR')} FCFA`
}

function formatDate(value) {
    return new Date(value).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' })
}
</script>

<template>
    <Head title="Tableau de bord" />

    <AdminLayout>
        <h1 class="mb-6 text-2xl font-bold text-gray-900">Tableau de bord</h1>

        <!-- Chiffres clés -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <component
                :is="card.href ? Link : 'div'"
                v-for="card in cards"
                :key="card.label"
                :href="card.href || undefined"
                class="rounded-2xl bg-white p-5 shadow-sm"
                :class="[
                    card.href ? 'transition hover:shadow-md' : '',
                    card.highlight ? 'ring-2 ring-red-300' : '',
                ]"
            >
                <p class="flex items-center justify-between text-sm text-gray-500">
                    {{ card.label }}
                    <span v-if="card.href" class="text-gray-300" aria-hidden="true">→</span>
                </p>
                <p class="mt-1 text-2xl font-bold" :class="card.color">{{ card.value }}</p>
            </component>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-6 xl:grid-cols-3">

            <!-- Dernières commandes -->
            <div class="rounded-2xl bg-white p-6 shadow-sm xl:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Dernières commandes</h2>
                    <Link :href="route('admin.orders.index')" class="text-sm text-blue-600 hover:underline">
                        Tout voir →
                    </Link>
                </div>

                <p v-if="!recentOrders.length" class="py-6 text-center text-sm text-gray-500">
                    Aucune commande pour le moment.
                </p>

                <ul v-else class="divide-y divide-gray-100">
                    <li v-for="order in recentOrders" :key="order.id">
                        <Link
                            :href="route('admin.orders.show', order.reference)"
                            class="flex flex-wrap items-center justify-between gap-2 py-3 text-sm hover:bg-gray-50"
                        >
                            <div>
                                <p class="font-semibold text-gray-900">{{ order.reference }}</p>
                                <p class="text-gray-500">{{ order.user?.name }} · {{ formatDate(order.created_at) }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <OrderStatusBadge :status="order.status" :label="statusLabels[order.status]" />
                                <span class="font-semibold">{{ formatFcfa(order.total) }}</span>
                            </div>
                        </Link>
                    </li>
                </ul>
            </div>

            <!-- Stock faible -->
            <div class="rounded-2xl bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">Stock faible</h2>

                <p v-if="!lowStock.length" class="py-6 text-center text-sm text-gray-500">
                    Tous les stocks sont suffisants.
                </p>

                <ul v-else class="divide-y divide-gray-100">
                    <li v-for="product in lowStock" :key="product.id" class="flex items-center justify-between py-3 text-sm">
                        <Link :href="route('admin.products.edit', product.id)" class="text-gray-900 hover:text-blue-600">
                            {{ product.name }}
                        </Link>
                        <span
                            class="rounded-full px-2 py-0.5 text-xs font-semibold"
                            :class="product.stock === 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800'"
                        >
                            {{ product.stock === 0 ? 'Rupture' : `${product.stock} restant(s)` }}
                        </span>
                    </li>
                </ul>
            </div>

        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">

            <!-- Messages à traiter -->
            <div class="rounded-2xl bg-white p-6 shadow-sm xl:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Messages à traiter</h2>
                    <Link :href="route('admin.contact-messages.index')" class="text-sm text-blue-600 hover:underline">
                        Tout voir →
                    </Link>
                </div>

                <p v-if="!recentMessages.length" class="py-6 text-center text-sm text-gray-500">
                    Aucun message en attente. 🎉
                </p>

                <ul v-else class="divide-y divide-gray-100">
                    <li v-for="message in recentMessages" :key="message.id">
                        <Link
                            :href="route('admin.contact-messages.show', message.id)"
                            class="flex flex-wrap items-center justify-between gap-2 py-3 text-sm hover:bg-gray-50"
                        >
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-gray-900">{{ message.subject }}</p>
                                <p class="text-gray-500">
                                    {{ message.name }} · {{ formatDate(message.created_at) }}
                                    <span v-if="message.order" class="text-blue-600"> · {{ message.order.reference }}</span>
                                </p>
                            </div>
                            <ContactStatusBadge :status="message.status" :label="message.status_label" />
                        </Link>
                    </li>
                </ul>
            </div>

            <!-- Accès rapides -->
            <div class="rounded-2xl bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">Accès rapides</h2>

                <ul class="space-y-1">
                    <li v-for="item in shortcuts" :key="item.label">
                        <Link
                            :href="item.href"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-blue-600"
                        >
                            <span>{{ item.icon }}</span>
                            {{ item.label }}
                        </Link>
                    </li>
                </ul>
            </div>

        </div>
    </AdminLayout>
</template>
