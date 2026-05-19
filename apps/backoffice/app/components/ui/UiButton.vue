<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  variant?: 'primary' | 'secondary' | 'outline' | 'soft' | 'danger' | 'success';
  size?: 'xs' | 'sm' | 'md' | 'lg';
  type?: 'button' | 'submit' | 'reset';
  disabled?: boolean;
  loading?: boolean;
  icon?: string;
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'xs',
  type: 'button',
  disabled: false,
  loading: false,
});

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'xs': return 'px-3 py-1.5 text-xs font-bold';
    case 'sm': return 'px-4 py-2 text-xs font-bold';
    case 'md': return 'px-5 py-2.5 text-sm font-semibold';
    case 'lg': return 'px-6 py-3 text-base font-semibold';
  }
});

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'primary':
      return 'border border-indigo-500 text-indigo-400 bg-indigo-500/5 hover:bg-indigo-600 hover:text-white shadow-sm shadow-indigo-500/10 focus:ring-indigo-500/50';
    case 'secondary':
      return 'border border-amber-500 text-amber-400 bg-amber-500/5 hover:bg-amber-600 hover:text-white shadow-sm shadow-amber-500/10 focus:ring-amber-500/50';
    case 'success':
      return 'border border-emerald-500 text-emerald-400 bg-emerald-500/5 hover:bg-emerald-600 hover:text-white shadow-sm shadow-emerald-500/10 focus:ring-emerald-500/50';
    case 'danger':
      return 'border border-rose-500/30 text-rose-400 bg-rose-500/5 hover:bg-rose-600 hover:text-white shadow-sm shadow-rose-500/10 focus:ring-rose-500/50';
    case 'outline':
      return 'border border-slate-800 text-slate-300 bg-slate-900 hover:bg-slate-850 hover:text-white focus:ring-slate-700/50';
    case 'soft':
      return 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-850 focus:ring-slate-700/50';
  }
});
</script>

<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'inline-flex items-center justify-center gap-1.5 rounded-lg border transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 disabled:cursor-default disabled:pointer-events-none disabled:opacity-50',
      sizeClasses,
      variantClasses
    ]"
  >
    <iconify-icon
      v-if="loading"
      icon="tabler:spinner"
      class="animate-spin text-sm"
    ></iconify-icon>
    <iconify-icon
      v-else-if="icon"
      :icon="icon"
      class="text-sm"
    ></iconify-icon>
    <slot></slot>
  </button>
</template>
