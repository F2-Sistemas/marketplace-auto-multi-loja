<script setup lang="ts">
import { useI18n } from '~/composables/useI18n';
import type { StoreLayoutStyle } from '~/composables/useTenant';
import UiInput from '~/components/ui/UiInput.vue';

interface Props {
    modelValue: string;
    layoutStyle?: StoreLayoutStyle;
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
    <div
        :class="[
            'relative mx-auto mb-10 w-full max-w-2xl',
            {
                'rounded-2xl border border-slate-800 bg-slate-900/40 p-3 shadow-inner shadow-slate-950/40':
                    layoutStyle !== 'catalog',
                'rounded-2xl border border-slate-200 bg-white p-3 shadow-sm': layoutStyle === 'catalog',
            },
        ]"
    >
        <div
            :class="[
                'pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4',
                {
                    'text-slate-400': layoutStyle !== 'catalog',
                    'text-slate-500': layoutStyle === 'catalog',
                },
            ]"
        >
            <iconify-icon icon="tabler:search" class="text-xl block"></iconify-icon>
        </div>

        <UiInput
            :modelValue="modelValue"
            @update:modelValue="onInput"
            :placeholder="t('search.placeholder')"
            :class="[
                'w-full rounded-xl py-3 pl-11 pr-10 text-base',
                {
                    'border-slate-800 bg-slate-950/20 shadow-inner shadow-slate-950/40 focus:border-slate-700':
                        layoutStyle !== 'catalog',
                    'border-slate-200 bg-slate-50 focus:border-brand-500': layoutStyle === 'catalog',
                },
            ]"
        />

        <button
            v-if="modelValue"
            @click="onInput('')"
            :class="[
                'absolute inset-y-0 right-0 flex items-center pr-4 transition-colors cursor-pointer',
                {
                    'text-slate-500 hover:text-slate-350': layoutStyle !== 'catalog',
                    'text-slate-400 hover:text-slate-700': layoutStyle === 'catalog',
                },
            ]"
        >
            <iconify-icon icon="tabler:x" class="text-xl block"></iconify-icon>
        </button>
    </div>
</template>
