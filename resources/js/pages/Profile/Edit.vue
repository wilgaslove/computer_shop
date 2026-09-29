<script setup>
import { Head } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import AccountNav from '@/Components/Shop/AccountNav.vue'
import DeleteUserForm from './Partials/DeleteUserForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
})
</script>

<template>
    <Head title="Mon profil" />

    <ShopLayout>
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">

            <AccountNav class="h-fit" />

            <section class="space-y-6 lg:col-span-3">
                <h1 class="text-2xl font-bold text-gray-900">Mon profil</h1>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </section>

        </div>
    </ShopLayout>
</template>
