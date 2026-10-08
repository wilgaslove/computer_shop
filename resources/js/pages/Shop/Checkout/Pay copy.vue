<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { addKkiapayListener, openKkiapayWidget, removeKkiapayListener } from 'kkiapay'
import ShopLayout from '@/Layouts/ShopLayout.vue'

const props = defineProps({
    order: { type: Object, required: true },
    amount: { type: Number, required: true },
    kkiapay: { type: Object, required: true },
    customer: { type: Object, default: () => ({}) },
})

// idle : en attente · verifying : vérification serveur en cours · failed : paiement refusé / fermé
const status = ref('idle')
const lastTransactionId = ref(null)
let autoOpenTimer = null

function formatFcfa(value) {
    return `${Number(value).toLocaleString('fr-FR')} FCFA`
}

function openWidget() {
    status.value = 'idle'

    openKkiapayWidget({
        amount: props.amount,
        key: props.kkiapay.key,
        api_key: props.kkiapay.key, // ancienne appellation du paramètre, par compatibilité
        sandbox: props.kkiapay.sandbox,
        theme: '#2563eb',
        position: 'center',
        name: props.customer.name,
        email: props.customer.email,
        phone: props.customer.phone,
        reason: `Commande ${props.order.reference}`,
        // La référence revient dans la notification serveur (webhook) pour retrouver la commande.
        partnerId: props.order.reference,
        data: props.order.reference,
    })
}

// Le serveur interroge KkiaPay lui-même : on n'envoie que l'identifiant de transaction.
function confirm(transactionId) {
    status.value = 'verifying'

    router.post(
        route('checkout.pay.confirm', props.order.reference),
        { transaction_id: transactionId },
        {
            preserveScroll: true,
            onFinish: () => {
                if (status.value === 'verifying') status.value = 'idle'
            },
        }
    )
}

function onSuccess(response) {
    lastTransactionId.value = response?.transactionId ?? null

    if (!lastTransactionId.value) {
        status.value = 'failed'
        return
    }

    confirm(lastTransactionId.value)
}

function onFailed() {
    status.value = 'failed'
}

onMounted(() => {
    addKkiapayListener('success', onSuccess)
    addKkiapayListener('failed', onFailed)

    // Ouverture automatique ; le bouton permet de la rouvrir si le client ferme la fenêtre.
    autoOpenTimer = setTimeout(openWidget, 400)
})

onBeforeUnmount(() => {
    clearTimeout(autoOpenTimer)
    removeKkiapayListener('success')
    removeKkiapayListener('failed')
})
</script>

<template>
    <Head title="Paiement de la commande" />

    <ShopLayout>
        <section class="py-8">
            <div class="mx-auto max-w-lg rounded-2xl border border-gray-200 bg-white p-8 text-center">

                <div class="text-5xl">💳</div>

                <h1 class="mt-4 text-2xl font-bold text-gray-900">Paiement sécurisé</h1>

                <p class="mt-2 text-gray-600">
                    Commande <span class="font-semibold text-gray-900">{{ order.reference }}</span>
                </p>

                <p class="mt-4 text-3xl font-extrabold text-blue-700">{{ formatFcfa(order.total) }}</p>

                <p class="mt-2 text-sm text-gray-500">
                    MTN, Moov, Celtis ou carte Visa. Le paiement est traité par KkiaPay.
                </p>

                <p v-if="kkiapay.sandbox" class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700">
                    Mode test (sandbox) : aucun argent réel n'est débité.
                </p>

                <!-- Vérification -->
                <div v-if="status === 'verifying'" class="mt-8 text-sm font-medium text-blue-700">
                    Vérification de votre paiement… ne fermez pas cette page.
                </div>

                <template v-else>
                    <div v-if="status === 'failed'" class="mt-6 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        Le paiement n'a pas abouti. Vous pouvez réessayer.
                    </div>

                    <button
                        type="button"
                        class="mt-8 w-full rounded-xl bg-blue-600 py-3 font-semibold text-white transition hover:bg-blue-700"
                        @click="openWidget"
                    >
                        {{ status === 'failed' ? 'Réessayer le paiement' : 'Payer maintenant' }}
                    </button>

                    <!-- Paiement effectué mais vérification interrompue (réseau) -->
                    <button
                        v-if="lastTransactionId"
                        type="button"
                        class="mt-3 w-full rounded-xl border border-gray-300 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                        @click="confirm(lastTransactionId)"
                    >
                        J'ai déjà payé : revérifier mon paiement
                    </button>

                    <Link
                        :href="route('account.orders.show', order.reference)"
                        class="mt-4 block text-sm text-gray-500 hover:text-blue-600"
                    >
                        Payer plus tard (voir ma commande)
                    </Link>
                </template>
            </div>
        </section>
    </ShopLayout>
</template>
