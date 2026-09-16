<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
    users: Object,
    roles: Array,
})

function changeRole(user, role) {
    if (!role) {
        return
    }

    if (
        !confirm(
            `Voulez-vous vraiment attribuer le rôle "${role}" à ${user.name} ?`
        )
    ) {
        return
    }

    router.patch(
        route('admin.users.role.update', user.id),
        {
            role: role,
        },
        {
            preserveScroll: true,
        }
    )
}
</script>

<template>

    <div class="max-w-7xl mx-auto p-8">

        <div class="mb-8">

            <h1 class="text-3xl font-bold">
                Utilisateurs
            </h1>

            <p class="text-gray-500 mt-1">
                Gérez les comptes et les rôles des utilisateurs.
            </p>

        </div>


        <div class="bg-white rounded-2xl shadow overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-50 border-b">

                    <tr>

                        <th class="text-left p-4">
                            Nom
                        </th>

                        <th class="text-left p-4">
                            Email
                        </th>

                        <th class="text-left p-4">
                            Rôle
                        </th>

                        <th class="text-left p-4">
                            Inscription
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <tr
                        v-for="user in users.data"
                        :key="user.id"
                        class="border-b last:border-0"
                    >

                        <td class="p-4 font-medium">
                            {{ user.name }}
                        </td>


                        <td class="p-4 text-gray-600">
                            {{ user.email }}
                        </td>


                        <td class="p-4">

                            <select
                                :value="user.roles[0]?.name"
                                @change="
                                    changeRole(
                                        user,
                                        $event.target.value
                                    )
                                "
                                :disabled="user.id === $page.props.auth.user.id"
                                class="border rounded-lg px-3 py-2"
                            >

                                <option
                                    v-for="role in roles"
                                    :key="role.id"
                                    :value="role.name"
                                >
                                    {{ role.name }}
                                </option>

                            </select>

                        </td>


                        <td class="p-4 text-gray-500">
                            {{ new Date(user.created_at).toLocaleDateString('fr-FR') }}
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</template>