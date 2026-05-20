<script setup lang="ts">
interface Props {
    variant?: 'neutral' | 'success' | 'warning' | 'store';
}
withDefaults(defineProps<Props>(), {
    variant: 'neutral',
});
</script>

<template>
    <span
        :class="[
            'inline-flex items-center px-2 py-0.5 rounded text-xs font-normal select-none border gap-1',
            variant === 'neutral' ? 'bg-slate-800/80 border-slate-700/60 text-slate-300' : '',
            variant === 'success' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : '',
            variant === 'warning' ? 'bg-amber-500/10 border-amber-500/20 text-amber-400' : '',
            variant === 'store' ? 'store-badge' : '',
        ]"
    >
        <slot></slot>
    </span>
</template>

<style scoped>
.store-badge {
    background-color: rgba(var(--store-primary-rgb, 245, 158, 11), 0.08);
    border-color: rgba(var(--store-primary-rgb, 245, 158, 11), 0.2);
    color: var(--store-accent-color, #fbbf24);
}
</style>
