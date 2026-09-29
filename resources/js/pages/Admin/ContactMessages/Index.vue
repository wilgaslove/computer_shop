<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ContactStatusBadge from '@/Components/Shop/ContactStatusBadge.vue'

const props = defineProps({
    messages: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    statuses: { type: Array, default: () => [] },
    statusCounts: { type: Object, default: () => ({}) },
})

const form = reactive({
    q: props.filters.q ?? '',
    status: props.filters.status ?? '',
})

function apply() {
    const params = Object.fromEntries(Object.entries(form).filter(([, v]) => v !== ''))

    router.get(route('admin.contact-messages.index'), params, {
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
    apply()
}

function formatDate(value) {
    return new Date(value).toLocaleString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const hasFilters = () => form.q || form.status
</script>

<template>
    <Head title="Messages de contact" />

    <AdminLayout>
        <h1 class="mb-6 text-2xl font-bold text-gray-900">Messages de contact</h1>

        <!-- Onglets de statut -->
        <div class="mb-4 flex flex-wrap gap-2">
            <button
                type="button"
                class="rounded-full px-4 py-2 text-sm font-medium transition"
                :class="form.status === '' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                @click="setStatus('')"
            >
                Tous
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

        <!-- Recherche -->
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
            <input
                v-model="form.q"
                type="search"
                placeholder="Rechercher : nom, email, téléphone, sujet, n° de commande…"
                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:max-w-md"
            >

            <button v-if="hasFilters()" type="button" class="text-sm text-gray-500 hover:text-blue-600" @click="reset">
                Réinitialiser
            </button>
        </div>

        <!-- Tableau -->
        <div class="overflow-x-auto rounded-2xl bg-white shadow-sm">
            <table class="w-full min-w-[720px] text-sm">
                <thead class="border-b bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="p-4">Statut</th>
                        <th class="p-4">Client</th>
                        <th class="p-4">Sujet</th>
                        <th class="p-4">Reçu le</th>
                        <th class="p-4"></th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="!props.messages.data.length">
                        <td colspan="5" class="p-10 text-center text-gray-500">Aucun message trouvé.</td>
                    </tr>

                    <tr
                        v-for="message in props.messages.data"
                        :key="message.id"
                        class="border-b last:border-0 hover:bg-gray-50"
                    >
                        <td class="p-4">
                            <ContactStatusBadge :status="message.status" :label="message.status_label" />
                        </td>

                        <td class="p-4">
                            <p :class="message.status === 'new' ? 'font-semibold text-gray-900' : 'text-gray-900'">{{ message.name }}</p>
                            <p class="text-xs text-gray-500">{{ message.email }}</p>
                        </td>

                        <td class="max-w-xs p-4">
                            <p class="truncate" :class="message.status === 'new' ? 'font-semibold text-gray-900' : 'text-gray-700'">
                                {{ message.subject }}
                            </p>
                            <p v-if="message.order" class="text-xs text-blue-600">Commande {{ message.order.reference }}</p>
                        </td>

                        <td class="whitespace-nowrap p-4 text-gray-600">{{ formatDate(message.created_at) }}</td>

                        <td class="p-4 text-right">
                            <Link :href="route('admin.contact-messages.show', message.id)" class="font-medium text-blue-600 hover:underline">
                                Ouvrir
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="props.messages.links.length > 3" class="mt-6 flex flex-wrap justify-center gap-1">
            <template v-for="(link, i) in props.messages.links" :key="i">
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
