<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  variant?: 'default' | 'interactive';
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default'
});

const classes = computed(() => {
  const base = 'bg-white border border-neutral-200 rounded-card overflow-hidden shadow-card transition-all duration-220 ease-[cubic-bezier(0.2,0,0,1)]';
  
  if (props.variant === 'interactive') {
    return `${base} hover:shadow-card-hover hover:-translate-y-1 hover:border-neutral-300`;
  }
  
  return base;
});
</script>

<template>
  <div :class="classes">
    <div v-if="$slots.header" class="px-6 py-4 border-b border-neutral-150">
      <slot name="header"></slot>
    </div>
    <div class="p-6">
      <slot></slot>
    </div>
    <div v-if="$slots.footer" class="px-6 py-4 border-t border-neutral-150 bg-neutral-50/50">
      <slot name="footer"></slot>
    </div>
  </div>
</template>
