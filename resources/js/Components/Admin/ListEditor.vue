<script setup>
/**
 * Éditeur de liste : ajouter, supprimer, réordonner (↑ ↓).
 * Usage : <ListEditor v-model="liste" :make-item="() => ({ ... })"> <template #default="{ item, index }"> … </template> </ListEditor>
 */
const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    makeItem: { type: Function, required: true },
    addLabel: { type: String, default: 'Ajouter' },
    max: { type: Number, default: 20 },
    itemTitle: { type: Function, default: null },
})

const emit = defineEmits(['update:modelValue'])

function add() {
    emit('update:modelValue', [...props.modelValue, props.makeItem()])
}

function remove(index) {
    const list = [...props.modelValue]
    list.splice(index, 1)
    emit('update:modelValue', list)
}

function move(index, step) {
    const target = index + step
    if (target < 0 || target >= props.modelValue.length) return

    const list = [...props.modelValue]
    ;[list[index], list[target]] = [list[target], list[index]]
    emit('update:modelValue', list)
}

const btn = 'rounded-lg px-2 py-1 text-sm text-gray-500 transition hover:bg-gray-200 disabled:cursor-not-allowed disabled:opacity-30'
</script>

<template>
    <div class="space-y-3">
        <div
            v-for="(item, index) in modelValue"
            :key="index"
            class="rounded-xl border border-gray-200 bg-gray-50 p-4"
        >
            <div class="mb-3 flex items-center justify-between gap-2">
                <span class="truncate text-xs font-semibold text-gray-500">
                    #{{ index + 1 }}<template v-if="itemTitle && itemTitle(item, index)"> · {{ itemTitle(item, index) }}</template>
                </span>

                <div class="flex flex-shrink-0 gap-1">
                    <button type="button" :class="btn" :disabled="index === 0" title="Monter" @click="move(index, -1)">↑</button>
                    <button type="button" :class="btn" :disabled="index === modelValue.length - 1" title="Descendre" @click="move(index, 1)">↓</button>
                    <button type="button" :class="[btn, 'hover:!bg-red-100 hover:!text-red-600']" title="Supprimer" @click="remove(index)">✕</button>
                </div>
            </div>

            <slot :item="item" :index="index" />
        </div>

        <p v-if="!modelValue.length" class="py-2 text-center text-sm text-gray-400">Aucun élément.</p>

        <button
            type="button"
            class="rounded-xl border border-dashed border-gray-300 px-4 py-2 text-sm font-medium text-blue-600 transition hover:border-blue-400 hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="modelValue.length >= max"
            @click="add"
        >
            + {{ addLabel }}
        </button>
    </div>
</template>
