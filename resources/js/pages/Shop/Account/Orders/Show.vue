<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import AccountNav from '@/Components/Shop/AccountNav.vue'
import OrderStatusBadge from '@/Components/Shop/OrderStatusBadge.vue'

const props = defineProps({
    order: { type: Object, required: true },
    canCancel: { type: Boolean, default: false },
})

function formatFcfa(value) {
    return `${Number(value).toLocaleString('fr-FR')} FCFA`
}

function formatDate(value) {
    return new Date(value).toLocaleString('fr-FR', { dateStyle: 'long', timeStyle: 'short' })
}

function cancelOrder() {
    if (!confirm('Annuler cette commande ? Cette action est définitive.')) return

    router.patch(route('account.orders.cancel', props.order.reference), {}, { preserveScroll: true })
}
</script>

<template>
    <Head :title="`Commande ${props.order.reference}`" />

    <ShopLayout>
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">

            <AccountNav class="h-fit" />

            <section class="space-y-6 lg:col-span-3">

                <Link :href="route('account.orders.index')" class="text-sm text-gray-500 hover:text-blue-600">
                    ← Retour à mes commandes
                </Link>

                <!-- En-tête -->
                <div class="flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="break-all text-xl font-bold text-gray-900">{{ props.order.reference }}</h1>
                        <p class="mt-1 text-sm text-gray-500">Passée le {{ formatDate(props.order.created_at) }}</p>
                    </div>
                    <OrderStatusBadge :status="props.order.status" :label="props.order.status_label" />
                </div>

                <!-- Articles -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-2 text-lg font-semibold text-gray-900">Articles</h2>

                    <ul class="divide-y divide-gray-100">
                        <li
                            v-for="item in props.order.items"
                            :key="item.id"
                            class="flex items-center gap-4 py-4"
                        >
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
                            </div>

                            <span class="font-semibold text-gray-900">{{ formatFcfa(item.subtotal) }}</span>
                        </li>
                    </ul>

                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                        <span class="text-gray-600">Total</span>
                        <span class="text-xl font-bold text-blue-700">{{ formatFcfa(props.order.total) }}</span>
                    </div>
                </div>

                <!-- Livraison / paiement -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 text-sm">
                        <h2 class="mb-2 text-lg font-semibold text-gray-900">Livraison</h2>
                        <p class="font-medium text-gray-900">{{ props.order.shipping_name }}</p>
                        <p class="text-gray-700">{{ props.order.address }}</p>
                        <p class="text-gray-700">{{ props.order.city }}</p>
                        <p class="text-gray-700">{{ props.order.phone }}</p>
                        <p v-if="props.order.notes" class="mt-2 text-gray-500">Note : {{ props.order.notes }}</p>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6 text-sm">
                        <h2 class="mb-2 text-lg font-semibold text-gray-900">Paiement</h2>
                        <p class="font-medium text-gray-900">{{ props.order.payment_method_label }}</p>
                        <p class="text-gray-700">{{ props.order.payment_status_label }}</p>
                    </div>
                </div>

                <!-- Aide -->
                <div class="flex flex-col items-start justify-between gap-3 rounded-2xl border border-blue-100 bg-blue-50 p-6 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="font-semibold text-gray-900">Un problème avec cette commande ?</h2>
                        <p class="text-sm text-gray-600">Notre équipe reçoit votre message avec la référence déjà renseignée.</p>
                    </div>

                    <Link
                        :href="route('contact', { order: props.order.reference })"
                        class="whitespace-nowrap rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Contacter le service client
                    </Link>
                </div>

                <!-- Annulation -->
                <div v-if="props.canCancel" class="text-right">
                    <button
                        type="button"
                        class="rounded-xl border border-red-300 px-5 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                        @click="cancelOrder"
                    >
                        Annuler la commande
                    </button>
                </div>

            </section>
        </div>
    </ShopLayout>
</template>
