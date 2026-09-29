<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import OrderStatusBadge from '@/Components/Shop/OrderStatusBadge.vue'

const props = defineProps({
    orders: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    statusCounts: { type: Object, default: () => ({}) },
    statuses: { type: Array, default: () => [] },
    paymentStatuses: { type: Array, default: () => [] },
})

const form = reactive({
    q: props.filters.q ?? '',
    status: props.filters.status ?? '',
    payment_status: props.filters.payment_status ?? '',
})

function apply() {
    const params = Object.fromEntries(Object.entries(form).filter(([, v]) => v !== ''))

    router.get(route('admin.orders.index'), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

// Recherche avec un léger délai pour ne pas requêter à chaque frappe
let timer = null
watch(() => form.q, () => {
    clearTimeout(timer)
    timer = setTimeout(apply, 350)
})

function setStatus(status) {
    form.status = status
    apply()
}

function reset() {
    form.q = ''
    form.status = ''
    form.payment_status = ''
    apply()
}

function formatFcfa(value) {
    return `${Number(value).toLocaleString('fr-FR')} FCFA`
}

function formatDate(value) {
    return new Date(value).toLocaleString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const hasFilters = () => form.q || form.status || form.payment_status
</script>

<template>
    <Head title="Commandes" />

    <AdminLayout>
        <h1 class="mb-6 text-2xl font-bold text-gray-900">Commandes</h1>

        <!-- Onglets de statut -->
        <div class="mb-4 flex flex-wrap gap-2">
            <button
                type="button"
                class="rounded-full px-4 py-2 text-sm font-medium transition"
                :class="form.status === '' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                @click="setStatus('')"
            >
                Toutes
            </button>

            <button
                v-for="s in props.statuses"
                :key="s.value"
                type="button"
                class="rounded-full px-4 py-2 text-sm font-medium transition"
                :class="form.status === s.value ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                @click="setStatus(s.value)"
            >
                {{ s.label }}
                <span class="ml-1 opacity-70">({{ props.statusCounts[s.value] ?? 0 }})</span>
            </button>
        </div>

        <!-- Recherche + paiement -->
        <div class="mb-4 flex flex-col gap-3 sm:flex-row">
            <input
                v-model="form.q"
                type="search"
                placeholder="Rechercher : référence, nom, téléphone, email…"
                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:max-w-md"
            >

            <select
                v-model="form.payment_status"
                class="rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                @change="apply"
            >
                <option value="">Tous les paiements</option>
                <option v-for="p in props.paymentStatuses" :key="p.value" :value="p.value">{{ p.label }}</option>
            </select>

            <button v-if="hasFilters()" type="button" class="text-sm text-gray-500 hover:text-blue-600" @click="reset">
                Réinitialiser
            </button>
        </div>

        <!-- Tableau -->
        <div class="overflow-x-auto rounded-2xl bg-white shadow-sm">
            <table class="w-full min-w-[720px] text-sm">
                <thead class="border-b bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="p-4">Référence</th>
                        <th class="p-4">Client</th>
                        <th class="p-4">Date</th>
                        <th class="p-4">Total</th>
                        <th class="p-4">Paiement</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4"></th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="!props.orders.data.length">
                        <td colspan="7" class="p-10 text-center text-gray-500">Aucune commande trouvée.</td>
                    </tr>

                    <tr v-for="order in props.orders.data" :key="order.id" class="border-b last:border-0 hover:bg-gray-50">
                        <td class="p-4 font-semibold text-gray-900">{{ order.reference }}</td>
                        <td class="p-4">
                            <p class="text-gray-900">{{ order.shipping_name }}</p>
                            <p class="text-xs text-gray-500">{{ order.user?.email }}</p>
                        </td>
                        <td class="p-4 text-gray-600">{{ formatDate(order.created_at) }}</td>
                        <td class="whitespace-nowrap p-4 font-semibold">{{ formatFcfa(order.total) }}</td>
                        <td class="p-4">
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-semibold"
                                :class="order.payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700'"
                            >
                                {{ order.payment_status_label }}
                            </span>
                        </td>
                        <td class="p-4"><OrderStatusBadge :status="order.status" :label="order.status_label" /></td>
                        <td class="p-4 text-right">
                            <Link :href="route('admin.orders.show', order.reference)" class="font-medium text-blue-600 hover:underline">
                                Détail
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="props.orders.links.length > 3" class="mt-6 flex flex-wrap justify-center gap-1">
            <template v-for="(link, i) in props.orders.links" :key="i">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    preserve-scroll
                    class="rounded-lg px-3 py-2 text-sm"
                    :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                    v-html="link.label"
                />
                <span v-else class="px-3 py-2 text-sm text-gray-300" v-html="link.label" />
            </template>
        </div>
    </AdminLayout>
</template>
