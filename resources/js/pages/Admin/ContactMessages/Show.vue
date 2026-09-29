<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ContactStatusBadge from '@/Components/Shop/ContactStatusBadge.vue'
import OrderStatusBadge from '@/Components/Shop/OrderStatusBadge.vue'

const props = defineProps({
    message: { type: Object, required: true },
    hasAttachment: { type: Boolean, default: false },
    statuses: { type: Array, default: () => [] },
})

const replyForm = useForm({ body: '' })

function sendReply() {
    replyForm.post(route('admin.contact-messages.reply', props.message.id), {
        preserveScroll: true,
        onSuccess: () => replyForm.reset('body'),
    })
}

// Libellé des actions (le libellé du statut seul serait ambigu sur un bouton)
const actionLabels = {
    new: 'Remettre en « Nouveau »',
    in_progress: 'Prendre en charge',
    answered: 'Marquer comme répondu',
    closed: 'Marquer comme traité (clôturer)',
}

function setStatus(status) {
    router.patch(route('admin.contact-messages.status', props.message.id), { status }, { preserveScroll: true })
}

function formatFcfa(value) {
    return `${Number(value).toLocaleString('fr-FR')} FCFA`
}

function formatDate(value) {
    return new Date(value).toLocaleString('fr-FR', { dateStyle: 'long', timeStyle: 'short' })
}
</script>

<template>
    <Head :title="`Message : ${props.message.subject}`" />

    <AdminLayout>
        <Link :href="route('admin.contact-messages.index')" class="text-sm text-gray-500 hover:text-blue-600">
            ← Retour aux messages
        </Link>

        <div class="mb-6 mt-3 flex flex-wrap items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900">{{ props.message.subject }}</h1>
            <ContactStatusBadge :status="props.message.status" :label="props.message.status_label" />
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            <!-- Colonne principale -->
            <div class="space-y-6 xl:col-span-2">

                <!-- Message du client -->
                <section class="rounded-2xl bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">
                        <strong class="text-gray-900">{{ props.message.name }}</strong>
                        · {{ formatDate(props.message.created_at) }}
                    </p>

                    <p class="mt-4 whitespace-pre-line text-gray-800">{{ props.message.message }}</p>

                    <a
                        v-if="props.hasAttachment"
                        :href="route('admin.contact-messages.attachment', props.message.id)"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-4 inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        📎 Télécharger la pièce jointe
                    </a>
                </section>

                <!-- Historique des réponses -->
                <section v-if="props.message.replies.length" class="rounded-2xl bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Réponses envoyées</h2>

                    <ul class="space-y-4">
                        <li
                            v-for="reply in props.message.replies"
                            :key="reply.id"
                            class="rounded-xl border-l-4 border-blue-500 bg-blue-50 p-4"
                        >
                            <p class="text-xs text-gray-500">
                                {{ reply.admin?.name ?? 'Équipe' }} · {{ formatDate(reply.created_at) }}
                            </p>
                            <p class="mt-2 whitespace-pre-line text-gray-800">{{ reply.body }}</p>
                        </li>
                    </ul>
                </section>

                <!-- Répondre -->
                <section class="rounded-2xl bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900">Répondre</h2>
                    <p class="mb-4 text-sm text-gray-500">
                        La réponse est envoyée par e-mail à <strong>{{ props.message.email }}</strong>.
                    </p>

                    <form @submit.prevent="sendReply">
                        <textarea
                            v-model="replyForm.body"
                            rows="6"
                            maxlength="5000"
                            placeholder="Votre réponse…"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        />
                        <p v-if="replyForm.errors.body" class="mt-1 text-sm text-red-600">{{ replyForm.errors.body }}</p>

                        <button
                            type="submit"
                            class="mt-3 rounded-xl bg-blue-600 px-6 py-2.5 font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60"
                            :disabled="replyForm.processing || !replyForm.body.trim()"
                        >
                            {{ replyForm.processing ? 'Envoi…' : 'Envoyer la réponse' }}
                        </button>
                    </form>
                </section>
            </div>

            <!-- Colonne latérale -->
            <aside class="space-y-6">

                <!-- Actions de statut -->
                <section class="rounded-2xl bg-white p-6 shadow-sm">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900">Statut</h2>

                    <div class="flex flex-col gap-2">
                        <button
                            v-for="s in props.statuses.filter((s) => s.value !== props.message.status)"
                            :key="s.value"
                            type="button"
                            class="rounded-xl border px-4 py-2 text-left text-sm font-medium transition"
                            :class="s.value === 'closed'
                                ? 'border-green-300 text-green-700 hover:bg-green-50'
                                : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
                            @click="setStatus(s.value)"
                        >
                            {{ actionLabels[s.value] ?? s.label }}
                        </button>
                    </div>
                </section>

                <!-- Client -->
                <section class="rounded-2xl bg-white p-6 text-sm shadow-sm">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900">Client</h2>

                    <p class="font-medium text-gray-900">{{ props.message.name }}</p>
                    <p><a :href="`mailto:${props.message.email}`" class="break-all text-blue-600 hover:underline">{{ props.message.email }}</a></p>
                    <p v-if="props.message.phone" class="text-gray-700">{{ props.message.phone }}</p>

                    <p class="mt-3 text-xs text-gray-500">
                        {{ props.message.user ? 'Compte client : ' + props.message.user.email : 'Visiteur non connecté' }}
                    </p>
                </section>

                <!-- Commande associée -->
                <section v-if="props.message.order" class="rounded-2xl bg-white p-6 text-sm shadow-sm">
                    <h2 class="mb-3 text-lg font-semibold text-gray-900">Commande associée</h2>

                    <div class="flex items-center justify-between gap-2">
                        <Link
                            :href="route('admin.orders.show', props.message.order.reference)"
                            class="font-semibold text-blue-600 hover:underline"
                        >
                            #{{ props.message.order.reference }}
                        </Link>
                        <OrderStatusBadge :status="props.message.order.status" :label="props.message.order.status_label" />
                    </div>

                    <ul class="mt-3 divide-y divide-gray-100">
                        <li v-for="item in props.message.order.items" :key="item.id" class="flex justify-between gap-3 py-2">
                            <span class="text-gray-700">{{ item.quantity }} × {{ item.product_name }}</span>
                            <span class="whitespace-nowrap text-gray-500">{{ formatFcfa(item.subtotal) }}</span>
                        </li>
                    </ul>

                    <div class="mt-2 flex justify-between border-t border-gray-200 pt-3 font-semibold text-gray-900">
                        <span>Total</span>
                        <span>{{ formatFcfa(props.message.order.total) }}</span>
                    </div>

                    <p class="mt-2 text-xs text-gray-500">
                        {{ props.message.order.payment_method_label }} · {{ props.message.order.payment_status_label }}
                    </p>
                </section>
            </aside>
        </div>
    </AdminLayout>
</template>
