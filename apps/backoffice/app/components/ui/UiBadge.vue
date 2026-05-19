<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  variant?: 'success' | 'danger' | 'warning' | 'info' | 'indigo';
  pulse?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'info',
  pulse: false,
});

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'success':
      return 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400';
    case 'danger':
      return 'bg-rose-500/10 border-rose-500/20 text-rose-400';
    case 'warning':
      return 'bg-amber-500/10 border-amber-500/20 text-amber-400';
    case 'indigo':
      return 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400';
    case 'info':
    default:
      return 'bg-slate-500/10 border-slate-500/20 text-slate-400';
  }
});

const pulseBgColor = computed(() => {
  switch (props.variant) {
    case 'success': return 'bg-emerald-400';
    case 'danger': return 'bg-rose-450';
    case 'warning': return 'bg-amber-400';
    case 'indigo': return 'bg-indigo-400';
    default: return 'bg-slate-400';
  }
});
</script>

<template>
  <span
    :class="[
      'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border text-[9px] font-extrabold uppercase tracking-wider leading-none',
      variantClasses
    ]"
  >
    <span
      v-if="pulse"
      :class="['w-1.5 h-1.5 rounded-full animate-ping', pulseBgColor]"
    ></span>
    <slot></slot>
  </span>
</template>
