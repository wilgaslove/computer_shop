<script setup>
import { Head, Link } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import AccountNav from '@/Components/Shop/AccountNav.vue'
import OrderStatusBadge from '@/Components/Shop/OrderStatusBadge.vue'

const props = defineProps({
    orders: { type: Object, required: true },
})

function formatFcfa(value) {
    return `${Number(value).toLocaleString('fr-FR')} FCFA`
}

function formatDate(value) {
    return new Date(value).toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' })
}
</script>

<template>
    <Head title="Mes commandes" />

    <ShopLayout>
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">

            <AccountNav class="h-fit" />

            <section class="lg:col-span-3">
                <h1 class="mb-6 text-2xl font-bold text-gray-900">Mes commandes</h1>

                <!-- Aucune commande -->
                <div
                    v-if="!props.orders.data.length"
                    class="rounded-2xl border border-gray-200 bg-white py-16 text-center"
                >
                    <div class="text-5xl">📦</div>
                    <h3 class="mt-4 text-xl font-semibold text-gray-700">Aucune commande pour le moment</h3>
                    <p class="mt-2 text-gray-500">Vos commandes apparaîtront ici.</p>
                    <Link
                        :href="route('shop.products')"
                        class="mt-6 inline-block rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700"
                    >
                        Voir les produits
                    </Link>
                </div>

                <!-- Liste -->
                <div v-else class="space-y-4">
                    <Link
                        v-for="order in props.orders.data"
                        :key="order.id"
                        :href="route('account.orders.show', order.reference)"
                        class="flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="font-semibold text-gray-900">{{ order.reference }}</p>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ formatDate(order.created_at) }} · {{ order.items_count }} article(s)
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <OrderStatusBadge :status="order.status" :label="order.status_label" />
                            <span class="font-bold text-blue-700">{{ formatFcfa(order.total) }}</span>
                        </div>
                    </Link>

                    <!-- Pagination -->
                    <div v-if="props.orders.links.length > 3" class="flex flex-wrap justify-center gap-1 pt-4">
                        <template v-for="(link, i) in props.orders.links" :key="i">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="rounded-lg px-3 py-2 text-sm"
                                :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                v-html="link.label"
                            />
                            <span v-else class="px-3 py-2 text-sm text-gray-300" v-html="link.label" />
                        </template>
                    </div>
                </div>
            </section>

        </div>
    </ShopLayout>
</template>
