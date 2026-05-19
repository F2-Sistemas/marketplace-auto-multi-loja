<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  modelValue: number;
  totalPages: number;
  disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false
});

const emit = defineEmits<{
  (e: 'update:modelValue', page: number): void;
}>();

const setPage = (page: number) => {
  if (page >= 1 && page <= props.totalPages && page !== props.modelValue && !props.disabled) {
    emit('update:modelValue', page);
  }
};

const pages = computed(() => {
  const current = props.modelValue;
  const total = props.totalPages;
  const list: number[] = [];
  
  if (total <= 5) {
    for (let i = 1; i <= total; i++) list.push(i);
  } else {
    // Show sliding page index (always showing up to 5 surrounding numbers)
    let start = Math.max(1, current - 2);
    let end = Math.min(total, current + 2);
    
    if (current <= 3) {
      end = 5;
    } else if (current >= total - 2) {
      start = total - 4;
    }
    
    for (let i = start; i <= end; i++) {
      list.push(i);
    }
  }
  
  return list;
});
</script>

<template>
  <div v-if="totalPages > 1" class="flex items-center gap-1.5 justify-center">
    <!-- Prev Button -->
    <button
      @click="setPage(modelValue - 1)"
      :disabled="modelValue === 1 || disabled"
      class="w-9 h-9 rounded-button border border-neutral-200 bg-white hover:bg-neutral-50 flex items-center justify-center text-neutral-500 hover:text-neutral-800 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white transition-all cursor-pointer"
      aria-label="Anterior"
    >
      <iconify-icon icon="tabler:chevron-left" class="text-base"></iconify-icon>
    </button>

    <!-- Pages list -->
    <button
      v-for="page in pages"
      :key="page"
      @click="setPage(page)"
      :disabled="disabled"
      class="w-9 h-9 font-semibold text-xs rounded-button border transition-all cursor-pointer"
      :class="page === modelValue
        ? 'bg-brand-500 text-white border-brand-500 hover:bg-brand-600'
        : 'bg-white text-neutral-600 border-neutral-200 hover:bg-neutral-50 hover:border-neutral-300'"
    >
      {{ page }}
    </button>

    <!-- Next Button -->
    <button
      @click="setPage(modelValue + 1)"
      :disabled="modelValue === totalPages || disabled"
      class="w-9 h-9 rounded-button border border-neutral-200 bg-white hover:bg-neutral-50 flex items-center justify-center text-neutral-500 hover:text-neutral-800 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white transition-all cursor-pointer"
      aria-label="Próximo"
    >
      <iconify-icon icon="tabler:chevron-right" class="text-base"></iconify-icon>
    </button>
  </div>
</template>
