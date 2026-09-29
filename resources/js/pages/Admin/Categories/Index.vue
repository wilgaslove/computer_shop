<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    categories: { type: Array, default: () => [] },
})

const page = usePage()
const error = computed(() => page.props.errors?.name)

function destroy(category) {
    if (!confirm(`Supprimer la catégorie « ${category.name} » ?`)) return

    router.delete(route('admin.categories.destroy', category.id), { preserveScroll: true })
}
</script>

<template>
    <Head title="Catégories" />

    <AdminLayout>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold text-gray-900">Catégories</h1>

            <Link
                :href="route('admin.categories.create')"
                class="rounded-xl bg-blue-600 px-5 py-2.5 font-semibold text-white transition hover:bg-blue-700"
            >
                + Nouvelle catégorie
            </Link>
        </div>

        <div v-if="error" class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ error }}
        </div>

        <div class="overflow-x-auto rounded-2xl bg-white shadow-sm">
            <table class="w-full min-w-[480px] text-sm">
                <thead class="border-b bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="p-4">Nom</th>
                        <th class="p-4">Produits</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="!categories.length">
                        <td colspan="3" class="p-10 text-center text-gray-500">Aucune catégorie pour le moment.</td>
                    </tr>

                    <tr v-for="category in categories" :key="category.id" class="border-b last:border-0 hover:bg-gray-50">
                        <td class="p-4 font-medium text-gray-900">{{ category.name }}</td>
                        <td class="p-4 text-gray-600">{{ category.products_count }}</td>
                        <td class="space-x-4 p-4 text-right">
                            <Link :href="route('admin.categories.edit', category.id)" class="font-medium text-blue-600 hover:underline">
                                Modifier
                            </Link>
                            <button
                                type="button"
                                class="font-medium text-red-600 hover:underline disabled:cursor-not-allowed disabled:text-gray-300 disabled:no-underline"
                                :disabled="category.products_count > 0"
                                :title="category.products_count > 0 ? 'Des produits utilisent cette catégorie' : ''"
                                @click="destroy(category)"
                            >
                                Supprimer
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
