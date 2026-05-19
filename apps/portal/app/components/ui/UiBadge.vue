<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  variant?: 'primary' | 'secondary' | 'neutral' | 'success' | 'warning' | 'info';
  size?: 'sm' | 'md';
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'neutral',
  size: 'md'
});

const classes = computed(() => {
  const base = 'inline-flex items-center font-bold uppercase tracking-wider select-none border rounded-badge transition-colors duration-150';
  
  const variants = {
    primary: 'bg-brand-50 text-brand-700 border-brand-100',
    secondary: 'bg-neutral-900 text-white border-transparent',
    neutral: 'bg-neutral-100 text-neutral-600 border-neutral-200',
    success: 'bg-emerald-50 text-emerald-700 border-emerald-100',
    warning: 'bg-amber-50 text-amber-700 border-amber-100',
    info: 'bg-blue-50 text-blue-700 border-blue-100'
  };

  const sizes = {
    sm: 'px-2 py-0.5 text-[9px] gap-1',
    md: 'px-2.5 py-1 text-[10px] gap-1.5'
  };

  return [base, variants[props.variant], sizes[props.size]].join(' ');
});
</script>

<template>
  <span :class="classes">
    <slot name="icon"></slot>
    <slot></slot>
  </span>
</template>
