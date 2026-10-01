<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    subscribers: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    activeCount: { type: Number, default: 0 },
})

const q = ref(props.filters.q ?? '')

let timer = null
watch(q, (value) => {
    clearTimeout(timer)
    timer = setTimeout(() => {
        router.get(route('admin.newsletter.index'), value ? { q: value } : {}, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        })
    }, 350)
})

function remove(subscriber) {
    if (!confirm(`Supprimer ${subscriber.email} ?`)) return

    router.delete(route('admin.newsletter.destroy', subscriber.id), { preserveScroll: true })
}

function formatDate(value) {
    return new Date(value).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' })
}
</script>

<template>
    <Head title="Newsletter" />

    <AdminLayout>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Newsletter</h1>
                <p class="text-sm text-gray-500">{{ props.activeCount }} inscrit(s) actif(s)</p>
            </div>

            <a
                :href="route('admin.newsletter.export')"
                class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
            >
                ⬇ Exporter en CSV
            </a>
        </div>

        <input
            v-model="q"
            type="search"
            placeholder="Rechercher une adresse e-mail…"
            class="mb-4 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:max-w-md"
        >

        <div class="overflow-x-auto rounded-2xl bg-white shadow-sm">
            <table class="w-full min-w-[480px] text-sm">
                <thead class="border-b bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="p-4">E-mail</th>
                        <th class="p-4">Inscrit le</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4"></th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="!props.subscribers.data.length">
                        <td colspan="4" class="p-10 text-center text-gray-500">Aucun inscrit.</td>
                    </tr>

                    <tr v-for="subscriber in props.subscribers.data" :key="subscriber.id" class="border-b last:border-0">
                        <td class="p-4 font-medium text-gray-900">{{ subscriber.email }}</td>
                        <td class="whitespace-nowrap p-4 text-gray-600">{{ formatDate(subscriber.created_at) }}</td>
                        <td class="p-4">
                            <span
                                class="rounded-full px-3 py-1 text-xs font-semibold"
                                :class="subscriber.unsubscribed_at ? 'bg-gray-200 text-gray-700' : 'bg-green-100 text-green-800'"
                            >
                                {{ subscriber.unsubscribed_at ? 'Désinscrit' : 'Actif' }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <button type="button" class="text-red-600 hover:underline" @click="remove(subscriber)">Supprimer</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="props.subscribers.links.length > 3" class="mt-6 flex flex-wrap justify-center gap-1">
            <template v-for="(link, i) in props.subscribers.links" :key="i">
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
