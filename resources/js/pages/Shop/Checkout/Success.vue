<script setup>
import { Head, Link } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'

const props = defineProps({
    order: { type: Object, required: true },
})

function formatFcfa(value) {
    return `${Number(value).toLocaleString('fr-FR')} FCFA`
}
</script>

<template>
    <Head title="Commande confirmée" />

    <ShopLayout>
        <section class="py-4">
            <div class="mx-auto max-w-3xl">

                <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center">
                    <div class="text-5xl">✅</div>

                    <h1 class="mt-4 text-2xl font-bold text-gray-900">
                        Merci pour votre commande !
                    </h1>

                    <p class="mt-2 text-gray-600">
                        Votre commande <span class="font-semibold text-gray-900">{{ props.order.reference }}</span>
                        a bien été enregistrée.
                    </p>
                </div>

                <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6">

                    <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <p class="text-gray-500">Livraison</p>
                            <p class="font-medium text-gray-900">{{ props.order.shipping_name }}</p>
                            <p class="text-gray-700">{{ props.order.address }}, {{ props.order.city }}</p>
                            <p class="text-gray-700">{{ props.order.phone }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500">Paiement</p>
                            <p class="font-medium text-gray-900">{{ props.order.payment_method_label }}</p>
                            <p class="text-gray-700">{{ props.order.payment_status_label }}</p>
                            <p class="mt-2 text-gray-500">Statut</p>
                            <p class="font-medium text-gray-900">{{ props.order.status_label }}</p>
                        </div>
                    </div>

                    <ul class="mt-6 divide-y divide-gray-100 border-t border-gray-100">
                        <li
                            v-for="item in props.order.items"
                            :key="item.id"
                            class="flex items-center justify-between gap-3 py-3 text-sm"
                        >
                            <div>
                                <p class="font-medium text-gray-900">{{ item.product_name }}</p>
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

                <div class="mt-6 text-center">
                    <Link
                        :href="route('shop.products.index')"
                        class="inline-block rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-700"
                    >
                        Continuer mes achats
                    </Link>
                </div>

            </div>
        </section>
    </ShopLayout>
</template>
