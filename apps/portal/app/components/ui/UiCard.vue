<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    variant?: 'default' | 'interactive';
    bodyClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
    bodyClass: '',
});

const classes = computed(() => {
    const base =
        'bg-white border border-neutral-200 rounded-card overflow-hidden shadow-card transition-all duration-220 ease-[cubic-bezier(0.2,0,0,1)]';

    if (props.variant === 'interactive') {
        return `${base} hover:shadow-card-hover hover:-translate-y-1 hover:border-neutral-300`;
    }

    return base;
});

const bodyClasses = computed(() => {
    if (!props.bodyClass) return 'p-6';

    const hasPadding = props.bodyClass
        .split(' ')
        .some(
            (c) =>
                c === 'p-0' ||
                c.startsWith('p-') ||
                c.startsWith('px-') ||
                c.startsWith('py-') ||
                c.startsWith('pt-') ||
                c.startsWith('pb-') ||
                c.startsWith('pl-') ||
                c.startsWith('pr-')
        );

    return hasPadding ? props.bodyClass : `p-6 ${props.bodyClass}`;
});
</script>

<template>
    <div :class="classes">
        <div v-if="$slots.header" class="px-6 py-4 border-b border-neutral-200">
            <slot name="header"></slot>
        </div>
        <div :class="bodyClasses">
            <slot></slot>
        </div>
        <div v-if="$slots.footer" class="px-6 py-4 border-t border-neutral-200 bg-neutral-50/50">
            <slot name="footer"></slot>
        </div>
    </div>
</template>
