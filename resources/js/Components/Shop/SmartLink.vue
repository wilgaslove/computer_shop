<script setup>
/**
 * Lien « intelligent » pour les liens saisis dans l'admin :
 * - /chemin           → navigation Inertia (sans rechargement)
 * - /#ancre, #ancre, https://… , mailto:, tel: → lien classique (https externe : nouvel onglet)
 * - vide              → simple texte (pas de lien)
 */
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    href: { type: String, default: '' },
})

const kind = computed(() => {
    const h = props.href || ''

    if (!h) return 'none'
    if (h.startsWith('/') && !h.includes('#')) return 'inertia'

    return 'anchor'
})

const external = computed(() => /^https?:\/\//i.test(props.href || ''))
</script>

<template>
    <Link v-if="kind === 'inertia'" :href="href"><slot /></Link>

    <a
        v-else-if="kind === 'anchor'"
        :href="href"
        :target="external ? '_blank' : undefined"
        :rel="external ? 'noopener noreferrer' : undefined"
    ><slot /></a>

    <span v-else><slot /></span>
</template>
