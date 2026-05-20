<script setup lang="ts">
import { useI18n } from '~/composables/useI18n';
import UiInput from '~/components/ui/UiInput.vue';

interface Props {
    modelValue: string;
}
defineProps<Props>();
const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const { t } = useI18n();

const onInput = (val: string) => {
    emit('update:modelValue', val);
};
</script>

<template>
    <div class="relative w-full max-w-2xl mx-auto mb-10">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
            <iconify-icon icon="tabler:search" class="text-xl block"></iconify-icon>
        </div>

        <UiInput
            :modelValue="modelValue"
            @update:modelValue="onInput"
            :placeholder="t('search.placeholder')"
            class="pl-11 pr-10 py-3 bg-slate-900/40 border-slate-800 focus:border-slate-700 w-full rounded-xl text-base shadow-inner shadow-slate-950/40"
        />

        <button
            v-if="modelValue"
            @click="onInput('')"
            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-slate-350 transition-colors cursor-pointer"
        >
            <iconify-icon icon="tabler:x" class="text-xl block"></iconify-icon>
        </button>
    </div>
</template>
