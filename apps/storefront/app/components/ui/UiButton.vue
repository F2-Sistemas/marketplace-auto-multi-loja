<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    variant?: 'primary' | 'secondary' | 'outline' | 'ghost';
    size?: 'sm' | 'md' | 'lg';
    loading?: boolean;
    disabled?: boolean;
    to?: string;
    href?: string;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'primary',
    size: 'md',
    loading: false,
    disabled: false,
});

const isComponent = computed(() => {
    if (props.to) return 'NuxtLink';
    if (props.href) return 'a';
    return 'button';
});

const baseClasses =
    'inline-flex items-center justify-center font-normal rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-950 disabled:opacity-50 disabled:cursor-not-allowed select-none cursor-pointer';

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'secondary':
            return 'bg-slate-800 text-slate-100 hover:bg-slate-700 border border-slate-700/50';
        case 'outline':
            return 'border border-slate-800 text-slate-300 hover:text-white hover:bg-slate-800/80 hover:border-slate-700';
        case 'ghost':
            return 'text-slate-400 hover:text-white hover:bg-slate-900';
        case 'primary':
        default:
            return 'store-btn';
    }
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'px-3 py-1.5 text-xs';
        case 'lg':
            return 'px-6 py-3 text-base';
        case 'md':
        default:
            return 'px-4 py-2.5 text-sm';
    }
});
</script>

<template>
    <component
        :is="isComponent"
        :to="to"
        :href="href"
        :disabled="isComponent === 'button' ? disabled || loading : undefined"
        :class="[baseClasses, variantClasses, sizeClasses]"
    >
        <iconify-icon v-if="loading" icon="fa7-solid:spinner" class="animate-spin mr-2 h-4 w-4"></iconify-icon>
        <slot></slot>
    </component>
</template>

<style scoped>
.store-btn {
    background: linear-gradient(135deg, var(--store-primary-from, #f59e0b) 0%, var(--store-primary-to, #ea580c) 100%);
    color: var(--store-btn-text, #0f172a);
    border: 1px solid transparent;
    box-shadow: 0 4px 12px rgba(var(--store-primary-rgb, 245, 158, 11), 0.12);
}
.store-btn:hover:not(:disabled) {
    opacity: 0.95;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(var(--store-primary-rgb, 245, 158, 11), 0.22);
}
.store-btn:active:not(:disabled) {
    transform: translateY(0);
}
</style>
