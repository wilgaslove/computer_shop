<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import OrderStatusBadge from '@/Components/Shop/OrderStatusBadge.vue'

defineProps({
    stats: { type: Object, required: true },
    recentOrders: { type: Array, default: () => [] },
    lowStock: { type: Array, default: () => [] },
})

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
            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Ventes (hors annulées)</p>
                <p class="mt-1 text-2xl font-bold text-blue-700">{{ formatFcfa(stats.sales) }}</p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Encaissé</p>
                <p class="mt-1 text-2xl font-bold text-green-700">{{ formatFcfa(stats.collected) }}</p>
            </div>

            <Link
                :href="route('admin.orders.index', { status: 'pending' })"
                class="rounded-2xl bg-white p-5 shadow-sm transition hover:shadow"
            >
                <p class="text-sm text-gray-500">Commandes en attente</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">{{ stats.pending_orders }}</p>
            </Link>

            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Commandes au total</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ stats.orders }}</p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Produits</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ stats.products }}</p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Catégories</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ stats.categories }}</p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Clients</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ stats.customers }}</p>
            </div>
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
    </AdminLayout>
</template>
