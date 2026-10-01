<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Field from '@/Components/Admin/Field.vue'
import ListEditor from '@/Components/Admin/ListEditor.vue'
import SectionCard from '@/Components/Admin/SectionCard.vue'

const props = defineProps({
    home: { type: Object, required: true },
    shop: { type: Object, required: true },
})

const form = useForm({
    home: props.home,
    shop: props.shop,
})

const tab = ref('home')

const err = (path) => form.errors[path] ?? ''
const errorCount = (prefix) => Object.keys(form.errors).filter((key) => key.startsWith(prefix)).length
const homeErrors = computed(() => errorCount('home.'))
const shopErrors = computed(() => errorCount('shop.'))

function save() {
    form.put(route('admin.site-content.update'), {
        preserveScroll: true,
        onError: (errors) => {
            // Bascule sur l'onglet qui contient la première erreur
            const first = Object.keys(errors)[0] ?? ''
            tab.value = first.startsWith('shop.') ? 'shop' : 'home'
        },
    })
}

const inp = 'w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500'
const icon = 'w-20 rounded-xl border-gray-300 text-center text-lg focus:border-blue-500 focus:ring-blue-500'
const linkHint = 'Ex : /shop, /promotions, /contact, /#faq ou https://…'

const tabClass = (name) => [
    'rounded-xl px-4 py-2 text-sm font-semibold transition',
    tab.value === name ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50',
]
</script>

<template>
    <Head title="Accueil du site" />

    <AdminLayout>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Accueil du site</h1>
                <p class="text-sm text-gray-500">Modifiez ici les textes de la page d'accueil, les coordonnées et le pied de page.</p>
            </div>

            <a
                :href="route('home')"
                target="_blank"
                rel="noopener"
                class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Voir la page d'accueil ↗
            </a>
        </div>

        <!-- Onglets -->
        <div class="mb-6 flex flex-wrap gap-2">
            <button type="button" :class="tabClass('home')" @click="tab = 'home'">
                Page d'accueil
                <span v-if="homeErrors" class="ml-1 rounded-full bg-red-500 px-2 text-xs text-white">{{ homeErrors }}</span>
            </button>
            <button type="button" :class="tabClass('shop')" @click="tab = 'shop'">
                Coordonnées & pied de page
                <span v-if="shopErrors" class="ml-1 rounded-full bg-red-500 px-2 text-xs text-white">{{ shopErrors }}</span>
            </button>
        </div>

        <form class="space-y-6 pb-24" novalidate @submit.prevent="save">

            <!-- =============================== PAGE D'ACCUEIL =============================== -->
            <div v-show="tab === 'home'" class="space-y-6">

                <!-- Texte d'accueil -->
                <SectionCard
                    title="Bannière d'accueil (texte)"
                    description="Affichée seulement s'il n'y a aucune bannière active dans « Bannières ». Dès qu'une bannière image est active, elle prend la place de ce bloc."
                >
                    <Link :href="route('admin.hero-sliders.index')" class="inline-block text-sm font-medium text-blue-600 hover:underline">
                        Gérer les bannières images →
                    </Link>

                    <Field label="Titre" :error="err('home.hero.title')">
                        <input v-model="form.home.hero.title" type="text" :class="inp">
                    </Field>
                    <Field label="Sous-titre" :error="err('home.hero.subtitle')">
                        <textarea v-model="form.home.hero.subtitle" rows="2" :class="inp" />
                    </Field>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <Field label="Bouton principal : texte" :error="err('home.hero.primary_label')">
                            <input v-model="form.home.hero.primary_label" type="text" :class="inp">
                        </Field>
                        <Field label="Bouton principal : lien" :hint="linkHint" :error="err('home.hero.primary_link')">
                            <input v-model="form.home.hero.primary_link" type="text" :class="inp">
                        </Field>
                        <Field label="Bouton secondaire : texte" :error="err('home.hero.secondary_label')">
                            <input v-model="form.home.hero.secondary_label" type="text" :class="inp">
                        </Field>
                        <Field label="Bouton secondaire : lien" :error="err('home.hero.secondary_link')">
                            <input v-model="form.home.hero.secondary_link" type="text" :class="inp">
                        </Field>
                    </div>
                </SectionCard>

                <!-- Ce que nous vendons -->
                <SectionCard
                    v-model:enabled="form.home.intro.enabled"
                    title="Ce que nous vendons"
                    description="Présentez vos grandes familles de produits."
                >
                    <div class="grid gap-4 sm:grid-cols-2">
                        <Field label="Titre" :error="err('home.intro.title')">
                            <input v-model="form.home.intro.title" type="text" :class="inp">
                        </Field>
                        <Field label="Sous-titre" :error="err('home.intro.subtitle')">
                            <input v-model="form.home.intro.subtitle" type="text" :class="inp">
                        </Field>
                    </div>

                    <ListEditor
                        v-model="form.home.intro.items"
                        :make-item="() => ({ icon: '💻', title: '', text: '', link: '/shop' })"
                        add-label="Ajouter une carte"
                        :max="8"
                        :item-title="(item) => item.title"
                    >
                        <template #default="{ item, index }">
                            <div class="grid gap-3 sm:grid-cols-[5rem_1fr_1fr]">
                                <Field label="Icône" :error="err(`home.intro.items.${index}.icon`)">
                                    <input v-model="item.icon" type="text" maxlength="16" :class="icon">
                                </Field>
                                <Field label="Titre" :error="err(`home.intro.items.${index}.title`)">
                                    <input v-model="item.title" type="text" :class="inp">
                                </Field>
                                <Field label="Lien" :hint="linkHint" :error="err(`home.intro.items.${index}.link`)">
                                    <input v-model="item.link" type="text" :class="inp">
                                </Field>
                            </div>
                            <div class="mt-3">
                                <Field label="Description" :error="err(`home.intro.items.${index}.text`)">
                                    <input v-model="item.text" type="text" :class="inp">
                                </Field>
                            </div>
                        </template>
                    </ListEditor>
                </SectionCard>

                <!-- Accessoires -->
                <SectionCard
                    v-model:enabled="form.home.accessories.enabled"
                    title="Complétez votre setup (accessoires)"
                    description="Raccourcis vers vos accessoires. Le lien /shop?q=souris ouvre le catalogue filtré sur « souris »."
                >
                    <div class="grid gap-4 sm:grid-cols-2">
                        <Field label="Titre" :error="err('home.accessories.title')">
                            <input v-model="form.home.accessories.title" type="text" :class="inp">
                        </Field>
                        <Field label="Sous-titre" :error="err('home.accessories.subtitle')">
                            <input v-model="form.home.accessories.subtitle" type="text" :class="inp">
                        </Field>
                    </div>

                    <ListEditor
                        v-model="form.home.accessories.items"
                        :make-item="() => ({ icon: '🔌', label: '', link: '/shop?q=' })"
                        add-label="Ajouter un accessoire"
                        :max="16"
                        :item-title="(item) => item.label"
                    >
                        <template #default="{ item, index }">
                            <div class="grid gap-3 sm:grid-cols-[5rem_1fr_1fr]">
                                <Field label="Icône" :error="err(`home.accessories.items.${index}.icon`)">
                                    <input v-model="item.icon" type="text" maxlength="16" :class="icon">
                                </Field>
                                <Field label="Nom" :error="err(`home.accessories.items.${index}.label`)">
                                    <input v-model="item.label" type="text" :class="inp">
                                </Field>
                                <Field label="Lien" :error="err(`home.accessories.items.${index}.link`)">
                                    <input v-model="item.link" type="text" :class="inp">
                                </Field>
                            </div>
                        </template>
                    </ListEditor>
                </SectionCard>

                <!-- Produits -->
                <SectionCard
                    title="Produits à découvrir"
                    description="Ces blocs se remplissent automatiquement avec vos produits. Un bloc sans produit n'est pas affiché."
                >
                    <div class="space-y-5">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <label class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-800">
                                <input v-model="form.home.products.new_enabled" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                Nouveautés (derniers produits ajoutés)
                            </label>
                            <div class="grid gap-3 sm:grid-cols-[1fr_1fr_8rem]">
                                <Field label="Titre" :error="err('home.products.new_title')"><input v-model="form.home.products.new_title" type="text" :class="inp"></Field>
                                <Field label="Sous-titre" :error="err('home.products.new_subtitle')"><input v-model="form.home.products.new_subtitle" type="text" :class="inp"></Field>
                                <Field label="Nombre (1-12)" :error="err('home.products.new_count')"><input v-model.number="form.home.products.new_count" type="number" min="1" max="12" :class="inp"></Field>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <label class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-800">
                                <input v-model="form.home.products.promo_enabled" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                Promotions (produits avec un prix promo)
                            </label>
                            <div class="grid gap-3 sm:grid-cols-[1fr_1fr_8rem]">
                                <Field label="Titre" :error="err('home.products.promo_title')"><input v-model="form.home.products.promo_title" type="text" :class="inp"></Field>
                                <Field label="Sous-titre" :error="err('home.products.promo_subtitle')"><input v-model="form.home.products.promo_subtitle" type="text" :class="inp"></Field>
                                <Field label="Nombre (1-12)" :error="err('home.products.promo_count')"><input v-model.number="form.home.products.promo_count" type="number" min="1" max="12" :class="inp"></Field>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <label class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-800">
                                <input v-model="form.home.products.popular_enabled" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                Les plus demandés (calculés d'après les commandes, hors annulées)
                            </label>
                            <div class="grid gap-3 sm:grid-cols-[1fr_1fr_8rem]">
                                <Field label="Titre" :error="err('home.products.popular_title')"><input v-model="form.home.products.popular_title" type="text" :class="inp"></Field>
                                <Field label="Sous-titre" :error="err('home.products.popular_subtitle')"><input v-model="form.home.products.popular_subtitle" type="text" :class="inp"></Field>
                                <Field label="Nombre (1-12)" :error="err('home.products.popular_count')"><input v-model.number="form.home.products.popular_count" type="number" min="1" max="12" :class="inp"></Field>
                            </div>
                        </div>
                    </div>
                </SectionCard>

                <!-- Bannière spéciale -->
                <SectionCard
                    v-model:enabled="form.home.banner.enabled"
                    title="Bannière spéciale"
                    description="Grand bandeau d'appel à l'action, entre les produits et les avantages."
                >
                    <Field label="Titre" :error="err('home.banner.title')">
                        <input v-model="form.home.banner.title" type="text" :class="inp">
                    </Field>
                    <Field label="Texte" :error="err('home.banner.text')">
                        <input v-model="form.home.banner.text" type="text" :class="inp">
                    </Field>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <Field label="Bouton : texte" :error="err('home.banner.button_label')">
                            <input v-model="form.home.banner.button_label" type="text" :class="inp">
                        </Field>
                        <Field label="Bouton : lien" :hint="linkHint" :error="err('home.banner.button_link')">
                            <input v-model="form.home.banner.button_link" type="text" :class="inp">
                        </Field>
                    </div>
                </SectionCard>

                <!-- Pourquoi nous -->
                <SectionCard
                    v-model:enabled="form.home.why.enabled"
                    title="Pourquoi acheter chez nous ?"
                    description="4 ou 5 avantages pour rassurer le client."
                >
                    <div class="grid gap-4 sm:grid-cols-2">
                        <Field label="Titre" :error="err('home.why.title')">
                            <input v-model="form.home.why.title" type="text" :class="inp">
                        </Field>
                        <Field label="Sous-titre" :error="err('home.why.subtitle')">
                            <input v-model="form.home.why.subtitle" type="text" :class="inp">
                        </Field>
                    </div>

                    <ListEditor
                        v-model="form.home.why.items"
                        :make-item="() => ({ icon: '✅', title: '', text: '' })"
                        add-label="Ajouter un avantage"
                        :max="8"
                        :item-title="(item) => item.title"
                    >
                        <template #default="{ item, index }">
                            <div class="grid gap-3 sm:grid-cols-[5rem_1fr]">
                                <Field label="Icône" :error="err(`home.why.items.${index}.icon`)">
                                    <input v-model="item.icon" type="text" maxlength="16" :class="icon">
                                </Field>
                                <Field label="Titre" :error="err(`home.why.items.${index}.title`)">
                                    <input v-model="item.title" type="text" :class="inp">
                                </Field>
                            </div>
                            <div class="mt-3">
                                <Field label="Description" :error="err(`home.why.items.${index}.text`)">
                                    <input v-model="item.text" type="text" :class="inp">
                                </Field>
                            </div>
                        </template>
                    </ListEditor>
                </SectionCard>

                <!-- Avis -->
                <SectionCard
                    v-model:enabled="form.home.reviews.enabled"
                    title="Avis clients"
                    description="Témoignages affichés sur l'accueil."
                >
                    <p class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                        N'affichez que de <strong>vrais avis</strong> de vos clients (avec leur accord). L'avis présent par défaut n'est qu'un exemple : remplacez-le avant d'activer la section.
                    </p>

                    <Field label="Titre" :error="err('home.reviews.title')">
                        <input v-model="form.home.reviews.title" type="text" :class="inp">
                    </Field>

                    <ListEditor
                        v-model="form.home.reviews.items"
                        :make-item="() => ({ rating: 5, text: '', author: '' })"
                        add-label="Ajouter un avis"
                        :max="8"
                        :item-title="(item) => item.author"
                    >
                        <template #default="{ item, index }">
                            <div class="grid gap-3 sm:grid-cols-[1fr_8rem]">
                                <Field label="Nom (ex : Jean A.)" :error="err(`home.reviews.items.${index}.author`)">
                                    <input v-model="item.author" type="text" :class="inp">
                                </Field>
                                <Field label="Note" :error="err(`home.reviews.items.${index}.rating`)">
                                    <select v-model.number="item.rating" :class="inp">
                                        <option v-for="n in [5, 4, 3, 2, 1]" :key="n" :value="n">{{ '★'.repeat(n) }}</option>
                                    </select>
                                </Field>
                            </div>
                            <div class="mt-3">
                                <Field label="Avis" :error="err(`home.reviews.items.${index}.text`)">
                                    <textarea v-model="item.text" rows="3" :class="inp" />
                                </Field>
                            </div>
                        </template>
                    </ListEditor>
                </SectionCard>

                <!-- FAQ -->
                <SectionCard
                    v-model:enabled="form.home.faq.enabled"
                    title="Questions fréquentes (FAQ)"
                    description="Affichée sur l'accueil et sur la page Contact."
                >
                    <Field label="Titre" :error="err('home.faq.title')">
                        <input v-model="form.home.faq.title" type="text" :class="inp">
                    </Field>

                    <ListEditor
                        v-model="form.home.faq.items"
                        :make-item="() => ({ q: '', a: '' })"
                        add-label="Ajouter une question"
                        :max="15"
                        :item-title="(item) => item.q"
                    >
                        <template #default="{ item, index }">
                            <Field label="Question" :error="err(`home.faq.items.${index}.q`)">
                                <input v-model="item.q" type="text" :class="inp">
                            </Field>
                            <div class="mt-3">
                                <Field label="Réponse" :error="err(`home.faq.items.${index}.a`)">
                                    <textarea v-model="item.a" rows="3" :class="inp" />
                                </Field>
                            </div>
                        </template>
                    </ListEditor>
                </SectionCard>

                <!-- Newsletter -->
                <SectionCard
                    v-model:enabled="form.home.newsletter.enabled"
                    title="Newsletter"
                    description="Formulaire d'inscription en bas de page. Les inscrits sont listés dans « Newsletter »."
                >
                    <Field label="Titre" :error="err('home.newsletter.title')">
                        <input v-model="form.home.newsletter.title" type="text" :class="inp">
                    </Field>
                    <Field label="Texte" :error="err('home.newsletter.text')">
                        <input v-model="form.home.newsletter.text" type="text" :class="inp">
                    </Field>
                    <Field label="Texte du bouton" :error="err('home.newsletter.button_label')">
                        <input v-model="form.home.newsletter.button_label" type="text" :class="[inp, 'sm:w-1/2']">
                    </Field>
                </SectionCard>
            </div>

            <!-- =============================== COORDONNÉES + FOOTER =============================== -->
            <div v-show="tab === 'shop'" class="space-y-6">

                <SectionCard
                    title="Boutique et coordonnées"
                    description="Utilisées dans le pied de page, la page Contact et les e-mails de réponse."
                >
                    <div class="grid gap-4 sm:grid-cols-2">
                        <Field label="Nom de la boutique" :error="err('shop.brand')">
                            <input v-model="form.shop.brand" type="text" :class="inp">
                        </Field>
                        <Field label="Slogan" :error="err('shop.tagline')">
                            <input v-model="form.shop.tagline" type="text" :class="inp">
                        </Field>
                        <Field label="Adresse" :error="err('shop.address')">
                            <input v-model="form.shop.address" type="text" :class="inp">
                        </Field>
                        <Field label="Horaires" :error="err('shop.hours')">
                            <input v-model="form.shop.hours" type="text" :class="inp">
                        </Field>
                        <Field label="Téléphone" :error="err('shop.phone')">
                            <input v-model="form.shop.phone" type="text" :class="inp">
                        </Field>
                        <Field label="WhatsApp" hint="Avec l'indicatif pays, ex : +229 …" :error="err('shop.whatsapp')">
                            <input v-model="form.shop.whatsapp" type="text" :class="inp">
                        </Field>
                        <Field label="E-mail" :error="err('shop.email')">
                            <input v-model="form.shop.email" type="email" :class="inp">
                        </Field>
                    </div>
                </SectionCard>

                <SectionCard title="Réseaux sociaux" description="Laissez vide pour ne pas afficher le lien. WhatsApp utilise le numéro ci-dessus.">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <Field label="Facebook (lien complet)" :error="err('shop.facebook')">
                            <input v-model="form.shop.facebook" type="url" placeholder="https://facebook.com/…" :class="inp">
                        </Field>
                        <Field label="Instagram (lien complet)" :error="err('shop.instagram')">
                            <input v-model="form.shop.instagram" type="url" placeholder="https://instagram.com/…" :class="inp">
                        </Field>
                    </div>
                </SectionCard>

                <SectionCard title="Carte (page Contact)" description="Position affichée sur la carte « Nous trouver ». Cotonou par défaut.">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <Field label="Latitude" :error="err('shop.map_lat')">
                            <input v-model.number="form.shop.map_lat" type="number" step="any" :class="inp">
                        </Field>
                        <Field label="Longitude" :error="err('shop.map_lng')">
                            <input v-model.number="form.shop.map_lng" type="number" step="any" :class="inp">
                        </Field>
                    </div>
                </SectionCard>

                <SectionCard title="Colonnes du pied de page" description="Jusqu'à 4 colonnes de liens (ex : Produits, Assistance, À propos).">
                    <ListEditor
                        v-model="form.shop.footer_columns"
                        :make-item="() => ({ title: '', links: [] })"
                        add-label="Ajouter une colonne"
                        :max="4"
                        :item-title="(column) => column.title"
                    >
                        <template #default="{ item: column, index: c }">
                            <Field label="Titre de la colonne" :error="err(`shop.footer_columns.${c}.title`)">
                                <input v-model="column.title" type="text" :class="inp">
                            </Field>

                            <div class="mt-4">
                                <p class="mb-2 text-sm font-medium text-gray-700">Liens</p>

                                <ListEditor
                                    v-model="column.links"
                                    :make-item="() => ({ label: '', url: '/' })"
                                    add-label="Ajouter un lien"
                                    :max="10"
                                    :item-title="(link) => link.label"
                                >
                                    <template #default="{ item: link, index: l }">
                                        <div class="grid gap-3 sm:grid-cols-2">
                                            <Field label="Texte" :error="err(`shop.footer_columns.${c}.links.${l}.label`)">
                                                <input v-model="link.label" type="text" :class="inp">
                                            </Field>
                                            <Field label="Lien" :hint="linkHint" :error="err(`shop.footer_columns.${c}.links.${l}.url`)">
                                                <input v-model="link.url" type="text" :class="inp">
                                            </Field>
                                        </div>
                                    </template>
                                </ListEditor>
                            </div>
                        </template>
                    </ListEditor>
                </SectionCard>
            </div>

            <!-- Barre d'enregistrement -->
            <div class="sticky bottom-0 -mx-2 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-white/95 p-4 shadow-lg backdrop-blur">
                <p v-if="Object.keys(form.errors).length" class="text-sm font-medium text-red-600">
                    {{ Object.keys(form.errors).length }} champ(s) à corriger.
                </p>
                <p v-else-if="form.isDirty" class="text-sm text-amber-700">Modifications non enregistrées.</p>
                <p v-else class="text-sm text-gray-400">Tout est enregistré.</p>

                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-8 py-2.5 font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Enregistrement…' : 'Enregistrer' }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
