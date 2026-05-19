<script setup lang="ts">
import { ref, computed } from 'vue';

interface Props {
  images: string[];
  title: string;
}

const props = withDefaults(defineProps<Props>(), {
  images: () => []
});

const activeIndex = ref(0);

const resolvedImages = computed(() => {
  if (props.images.length > 0) return props.images;
  return ['https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=1200&q=80'];
});

const activeImage = computed(() => resolvedImages.value[activeIndex.value]);

const setIndex = (index: number) => {
  activeIndex.value = index;
};

const prev = () => {
  activeIndex.value = (activeIndex.value - 1 + resolvedImages.value.length) % resolvedImages.value.length;
};

const next = () => {
  activeIndex.value = (activeIndex.value + 1) % resolvedImages.value.length;
};
</script>

<template>
  <div class="space-y-4">
    <!-- Big Main Stage -->
    <div class="relative aspect-video w-full rounded-card overflow-hidden bg-neutral-100 border border-neutral-200 group/gallery shadow-card">
      <img
        :src="activeImage"
        :alt="title"
        class="w-full h-full object-cover transition-all duration-300"
      />
      
      <!-- Controls absolute (only when > 1) -->
      <div v-if="resolvedImages.length > 1" class="absolute inset-0 flex items-center justify-between p-4 opacity-0 group-hover/gallery:opacity-100 transition-opacity">
        <button
          @click="prev"
          class="w-10 h-10 rounded-full bg-white/90 hover:bg-white text-neutral-800 flex items-center justify-center shadow-lg border border-neutral-150 transition active:scale-95 cursor-pointer"
          aria-label="Foto anterior"
        >
          <iconify-icon icon="tabler:chevron-left" class="text-xl"></iconify-icon>
        </button>
        <button
          @click="next"
          class="w-10 h-10 rounded-full bg-white/90 hover:bg-white text-neutral-800 flex items-center justify-center shadow-lg border border-neutral-150 transition active:scale-95 cursor-pointer"
          aria-label="Próxima foto"
        >
          <iconify-icon icon="tabler:chevron-right" class="text-xl"></iconify-icon>
        </button>
      </div>

      <!-- Counter Tag -->
      <div class="absolute bottom-4 right-4 px-3 py-1 bg-neutral-900/75 backdrop-blur-xs text-white text-xs font-bold rounded-full select-none">
        {{ activeIndex + 1 }} / {{ resolvedImages.length }}
      </div>
    </div>

    <!-- Thumbnails navigation -->
    <div v-if="resolvedImages.length > 1" class="grid grid-cols-5 gap-3">
      <button
        v-for="(img, idx) in resolvedImages"
        :key="idx"
        @click="setIndex(idx)"
        class="relative aspect-video rounded-lg overflow-hidden bg-neutral-150 border-2 transition-all cursor-pointer outline-none"
        :class="activeIndex === idx ? 'border-brand-500 ring-2 ring-brand-100 scale-95' : 'border-transparent hover:border-neutral-300'"
      >
        <img :src="img" :alt="title" class="w-full h-full object-cover" />
      </button>
    </div>
  </div>
</template>
