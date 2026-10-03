<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import OrderStatusBadge from '@/Components/Shop/OrderStatusBadge.vue'

const props = defineProps({
    order: { type: Object, required: true },
    transitions: { type: Array, default: () => [] },
    paymentStatuses: { type: Array, default: () => [] },
})

function formatFcfa(value) {
    return `${Number(value).toLocaleString('fr-FR')} FCFA`
}

function formatDate(value) {
    return new Date(value).toLocaleString('fr-FR', { dateStyle: 'long', timeStyle: 'short' })
}

function changeStatus(status) {
    const label = props.transitions.find(t => t.value === status)?.label ?? status
    const warning = status === 'cancelled'
        ? 'Annuler cette commande ? Les quantités seront remises en stock. Action définitive.'
        : `Passer la commande au statut « ${label} » ?`

    if (!confirm(warning)) return

    router.patch(route('admin.orders.status', props.order.reference), { status }, { preserveScroll: true })
}

function changePayment(payment_status) {
    router.patch(route('admin.orders.payment', props.order.reference), { payment_status }, { preserveScroll: true })
}

const nextPayment = () => props.order.payment_status === 'paid' ? 'unpaid' : 'paid'
</script>

<template>
    <Head :title="`Commande ${props.order.reference}`" />

    <AdminLayout>
        <Link :href="route('admin.orders.index')" class="text-sm text-gray-500 hover:text-blue-600">
            ← Retour aux commandes
        </Link>

        <div class="mb-6 mt-3 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ props.order.reference }}</h1>
                <p class="text-sm text-gray-500">Passée le {{ formatDate(props.order.created_at) }}</p>
            </div>
            <OrderStatusBadge :status="props.order.status" :label="props.order.status_label" />
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            <!-- Articles -->
            <div class="rounded-2xl bg-white p-6 shadow-sm xl:col-span-2">
                <h2 class="mb-2 text-lg font-semibold text-gray-900">Articles</h2>

                <ul class="divide-y divide-gray-100">
                    <li v-for="item in props.order.items" :key="item.id" class="flex items-center gap-4 py-4">
                        <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-xl bg-gray-100">
                            <img
                                v-if="item.product_image"
                                :src="`/storage/${item.product_image}`"
                                :alt="item.product_name"
                                class="h-full w-full object-cover"
                            >
                            <div v-else class="flex h-full w-full items-center justify-center text-2xl text-gray-300">💻</div>
                        </div>

                        <div class="min-w-0 flex-1 text-sm">
                            <p class="break-words font-medium text-gray-900">{{ item.product_name }}</p>
                            <p class="text-gray-500">{{ item.quantity }} × {{ formatFcfa(item.price) }}</p>
                            <p v-if="!item.product_id" class="text-xs text-amber-600">Produit supprimé du catalogue</p>
                        </div>

                        <span class="font-semibold">{{ formatFcfa(item.subtotal) }}</span>
                    </li>
                </ul>

                <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                    <span class="text-gray-600">Total</span>
                    <span class="text-xl font-bold text-blue-700">{{ formatFcfa(props.order.total) }}</span>
                </div>
            </div>

            <!-- Colonne de droite -->
            <div class="space-y-6">

                <!-- Actions statut -->
                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900">Statut</h2>

                    <p v-if="!props.transitions.length" class="text-sm text-gray-500">
                        Cette commande est terminée, son statut ne peut plus changer.
                    </p>

                    <div v-else class="flex flex-col gap-2">
                        <button
                            v-for="t in props.transitions"
                            :key="t.value"
                            type="button"
                            class="rounded-xl px-4 py-2 text-sm font-semibold transition"
                            :class="t.value === 'cancelled'
                                ? 'border border-red-300 text-red-600 hover:bg-red-50'
                                : 'bg-blue-600 text-white hover:bg-blue-700'"
                            @click="changeStatus(t.value)"
                        >
                            {{ t.value === 'cancelled' ? 'Annuler la commande' : `Passer à : ${t.label}` }}
                        </button>
                    </div>
                </div>

                <!-- Paiement -->
                <div class="rounded-2xl bg-white p-6 text-sm shadow-sm">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900">Paiement</h2>
                    <p class="font-medium text-gray-900">{{ props.order.payment_method_label }}</p>
                    <p class="mb-3 text-gray-600">{{ props.order.payment_status_label }}</p>

                    <button
                        v-if="props.order.status !== 'cancelled'"
                        type="button"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-50"
                        @click="changePayment(nextPayment())"
                    >
                        {{ props.order.payment_status === 'paid' ? 'Marquer comme non payée' : 'Marquer comme payée' }}
                    </button>
                </div>

                <!-- Client / livraison -->
                <div class="rounded-2xl bg-white p-6 text-sm shadow-sm">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900">Client</h2>
                    <template v-if="props.order.user">
                        <p class="font-medium text-gray-900">{{ props.order.user.name }}</p>
                        <p class="mb-4 text-gray-600">{{ props.order.user.email }}</p>
                    </template>
                    <p v-else class="mb-4 text-gray-500">Compte client supprimé</p>

                    <h2 class="mb-2 text-lg font-semibold text-gray-900">Livraison</h2>
                    <p class="font-medium text-gray-900">{{ props.order.shipping_name }}</p>
                    <p class="text-gray-700">{{ props.order.address }}</p>
                    <p class="text-gray-700">{{ props.order.city }}</p>
                    <a :href="`tel:${props.order.phone}`" class="text-blue-600 hover:underline">{{ props.order.phone }}</a>
                    <p v-if="props.order.notes" class="mt-2 text-gray-500">Note : {{ props.order.notes }}</p>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
