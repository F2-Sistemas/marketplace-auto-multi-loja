<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  variant?: 'primary' | 'secondary' | 'outline' | 'ghost' | 'soft' | 'danger';
  size?: 'sm' | 'md' | 'lg';
  disabled?: boolean;
  loading?: boolean;
  type?: 'button' | 'submit' | 'reset';
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'md',
  disabled: false,
  loading: false,
  type: 'button'
});

const classes = computed(() => {
  const base = 'inline-flex items-center justify-center font-semibold transition-all duration-180 ease-[cubic-bezier(0.2,0,0,1)] select-none outline-none active:scale-[0.98]';
  
  const variants = {
    primary: 'bg-brand-500 text-white hover:bg-brand-600 focus:ring-2 focus:ring-brand-200 focus:ring-offset-1',
    secondary: 'bg-neutral-900 text-white hover:bg-neutral-800 focus:ring-2 focus:ring-neutral-200 focus:ring-offset-1',
    outline: 'border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50 hover:border-neutral-300 focus:ring-2 focus:ring-neutral-100',
    ghost: 'text-neutral-600 hover:bg-neutral-50 hover:text-neutral-900',
    soft: 'bg-brand-50 text-brand-700 hover:bg-brand-100 focus:ring-2 focus:ring-brand-100',
    danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-2 focus:ring-red-200'
  };

  const sizes = {
    sm: 'h-8 px-3.5 text-xs rounded-button gap-1.5',
    md: 'h-10 px-5 text-sm rounded-button gap-2',
    lg: 'h-12 px-7 text-base rounded-button gap-2.5'
  };

  return [
    base,
    variants[props.variant],
    sizes[props.size],
    (props.disabled || props.loading) ? 'opacity-60 cursor-not-allowed pointer-events-none active:scale-100' : 'cursor-pointer'
  ].join(' ');
});
</script>

<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="classes"
  >
    <iconify-icon
      v-if="loading"
      icon="tabler:loader-2"
      class="animate-spin"
      :class="size === 'sm' ? 'text-sm' : 'text-base'"
    ></iconify-icon>
    <slot v-else name="icon"></slot>
    <slot></slot>
  </button>
</template>
