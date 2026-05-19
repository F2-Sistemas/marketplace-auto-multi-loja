<script setup lang="ts">
import { onMounted, onUnmounted, watch } from 'vue';

interface Props {
  isOpen: boolean;
  title?: string;
}

const props = withDefaults(defineProps<Props>(), {
  isOpen: false,
  title: ''
});

const emit = defineEmits<{
  (e: 'close'): void;
}>();

const close = () => {
  emit('close');
};

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && props.isOpen) {
    close();
  }
};

watch(() => props.isOpen, (open) => {
  if (open) {
    document.body.style.overflow = 'hidden';
    window.addEventListener('keydown', handleKeyDown);
  } else {
    document.body.style.overflow = '';
    window.removeEventListener('keydown', handleKeyDown);
  }
});

onMounted(() => {
  if (props.isOpen) {
    document.body.style.overflow = 'hidden';
    window.addEventListener('keydown', handleKeyDown);
  }
});

onUnmounted(() => {
  document.body.style.overflow = '';
  window.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex justify-end"
      role="dialog"
      aria-modal="true"
    >
      <!-- Backdrop -->
      <div
        class="fixed inset-0 bg-neutral-900/40 backdrop-blur-xs transition-opacity duration-220 ease-[cubic-bezier(0.2,0,0,1)] animate-in fade-in"
        @click="close"
      ></div>

      <!-- Content Drawer -->
      <div
        class="relative w-full max-w-sm h-full bg-white shadow-2xl flex flex-col z-10 transition-transform duration-220 ease-[cubic-bezier(0.2,0,0,1)] animate-in slide-in-from-right"
      >
        <!-- Header -->
        <div class="px-6 h-16 border-b border-neutral-150 flex items-center justify-between">
          <h3 class="font-bold text-base text-neutral-800">{{ title }}</h3>
          <button
            @click="close"
            class="w-8 h-8 rounded-lg hover:bg-neutral-50 flex items-center justify-center text-neutral-500 hover:text-neutral-800 transition-colors cursor-pointer"
            aria-label="Fechar"
          >
            <iconify-icon icon="tabler:x" class="text-lg"></iconify-icon>
          </button>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-6">
          <slot></slot>
        </div>

        <!-- Footer (Optional) -->
        <div v-if="$slots.footer" class="p-6 border-t border-neutral-150 bg-neutral-50/50">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </Teleport>
</template>
