<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'

const props = defineProps({
    items: { type: Array, default: () => [] },
    total: { type: [Number, String], default: 0 },
    paymentMethods: { type: Array, default: () => [] },
    defaults: { type: Object, default: () => ({}) },
})

const form = useForm({
    shipping_name: props.defaults.shipping_name ?? '',
    phone: '',
    city: '',
    address: '',
    notes: '',
    payment_method: props.paymentMethods[0]?.value ?? 'cash_on_delivery',
})

function formatFcfa(value) {
    return `${Number(value).toLocaleString('fr-FR')} FCFA`
}

function submit() {
    form.post(route('checkout.store'))
}
</script>

<template>
    <Head title="Validation de la commande" />

    <ShopLayout>
        <section class="py-4">
            <div class="mx-auto max-w-5xl">

                <h1 class="mb-8 text-3xl font-bold text-gray-900">
                    Finaliser ma commande
                </h1>

                <!-- Erreur globale (stock, produit indisponible…) -->
                <div
                    v-if="form.errors.cart"
                    class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
                >
                    {{ form.errors.cart }}
                    <Link :href="route('cart.index')" class="ml-1 font-semibold underline">
                        Retour au panier
                    </Link>
                </div>

                <form class="grid grid-cols-1 gap-8 lg:grid-cols-3" @submit.prevent="submit">

                    <!-- Formulaire -->
                    <div class="space-y-6 lg:col-span-2">

                        <!-- Livraison -->
                        <div class="rounded-2xl border border-gray-200 bg-white p-6">
                            <h2 class="mb-4 text-lg font-semibold text-gray-900">
                                Informations de livraison
                            </h2>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Nom complet</label>
                                    <input
                                        v-model="form.shipping_name"
                                        type="text"
                                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    >
                                    <p v-if="form.errors.shipping_name" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.shipping_name }}
                                    </p>
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Téléphone</label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        placeholder="+229 ..."
                                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    >
                                    <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.phone }}
                                    </p>
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Ville</label>
                                    <input
                                        v-model="form.city"
                                        type="text"
                                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    >
                                    <p v-if="form.errors.city" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.city }}
                                    </p>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Adresse de livraison</label>
                                    <textarea
                                        v-model="form.address"
                                        rows="2"
                                        placeholder="Quartier, rue, point de repère…"
                                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.address }}
                                    </p>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-sm font-medium text-gray-700">
                                        Note pour la livraison <span class="text-gray-400">(facultatif)</span>
                                    </label>
                                    <textarea
                                        v-model="form.notes"
                                        rows="2"
                                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.notes }}
                                    </p>
                                </div>

                            </div>
                        </div>

                        <!-- Paiement -->
                        <div class="rounded-2xl border border-gray-200 bg-white p-6">
                            <h2 class="mb-4 text-lg font-semibold text-gray-900">
                                Mode de paiement
                            </h2>

                            <div class="space-y-3">
                                <label
                                    v-for="method in props.paymentMethods"
                                    :key="method.value"
                                    class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition"
                                    :class="form.payment_method === method.value
                                        ? 'border-blue-600 bg-blue-50'
                                        : 'border-gray-200 hover:border-gray-300'"
                                >
                                    <input
                                        v-model="form.payment_method"
                                        type="radio"
                                        :value="method.value"
                                        class="text-blue-600 focus:ring-blue-500"
                                    >
                                    <span class="font-medium text-gray-900">{{ method.label }}</span>
                                </label>
                            </div>

                            <p v-if="form.errors.payment_method" class="mt-2 text-sm text-red-600">
                                {{ form.errors.payment_method }}
                            </p>
                        </div>

                    </div>

                    <!-- Récapitulatif -->
                    <div class="h-fit rounded-2xl border border-gray-200 bg-white p-6">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Récapitulatif
                        </h2>

                        <ul class="mt-4 divide-y divide-gray-100">
                            <li
                                v-for="item in props.items"
                                :key="item.product.id"
                                class="flex items-start justify-between gap-3 py-3 text-sm"
                            >
                                <div>
                                    <p class="font-medium text-gray-900">{{ item.product.name }}</p>
                                    <p class="text-gray-500">
                                        {{ item.quantity }} × {{ formatFcfa(item.product.current_price) }}
                                    </p>
                                </div>
                                <span class="whitespace-nowrap font-semibold text-gray-900">
                                    {{ formatFcfa(item.subtotal) }}
                                </span>
                            </li>
                        </ul>

                        <div class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4">
                            <span class="text-gray-600">Total</span>
                            <span class="text-xl font-bold text-blue-700">
                                {{ formatFcfa(props.total) }}
                            </span>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="mt-6 w-full rounded-xl bg-blue-600 py-3 font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ form.processing ? 'Validation…' : 'Confirmer la commande' }}
                        </button>

                        <Link
                            :href="route('cart.index')"
                            class="mt-3 block text-center text-sm text-gray-500 hover:text-blue-600"
                        >
                            ← Modifier mon panier
                        </Link>
                    </div>

                </form>
            </div>
        </section>
    </ShopLayout>
</template>
