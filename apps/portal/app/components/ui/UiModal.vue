<script setup lang="ts">
import { onMounted, onUnmounted, watch } from 'vue';

interface Props {
  isOpen: boolean;
  maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl';
}

const props = withDefaults(defineProps<Props>(), {
  isOpen: false,
  maxWidth: '2xl'
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

const widthClasses = {
  sm: 'max-w-sm',
  md: 'max-w-md',
  lg: 'max-w-lg',
  xl: 'max-w-xl',
  '2xl': 'max-w-2xl'
};
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      role="dialog"
      aria-modal="true"
    >
      <!-- Backdrop -->
      <div
        class="fixed inset-0 bg-neutral-900/60 backdrop-blur-xs transition-opacity duration-220 ease-[cubic-bezier(0.2,0,0,1)] animate-in fade-in"
        @click="close"
      ></div>

      <!-- Content Box -->
      <div
        class="relative bg-white border border-neutral-200 w-full rounded-card overflow-hidden shadow-modal z-10 transition-all duration-220 ease-[cubic-bezier(0.2,0,0,1)] animate-in fade-in zoom-in-95"
        :class="widthClasses[maxWidth]"
      >
        <!-- Close CTA -->
        <button
          @click="close"
          class="absolute top-4 right-4 z-10 w-8 h-8 rounded-lg hover:bg-neutral-100 flex items-center justify-center text-neutral-400 hover:text-neutral-700 transition-colors cursor-pointer"
          aria-label="Fechar"
        >
          <iconify-icon icon="tabler:x" class="text-lg"></iconify-icon>
        </button>

        <slot></slot>
      </div>
    </div>
  </Teleport>
</template>
