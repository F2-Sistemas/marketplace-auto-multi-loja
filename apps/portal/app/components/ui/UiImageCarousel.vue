<template>
  <div class="relative overflow-hidden w-full h-full">
    <Transition :name="transitionName" mode="out-in">
      <img
        :key="images[currentIndex].id"
        :src="images[currentIndex].url"
        :alt="images[currentIndex].alt"
        class="absolute inset-0 w-full h-full object-cover"
      />
    </Transition>

    <!-- Navigation Buttons -->
    <button
      v-if="images.length > 1"
      @click="prevImage"
      class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full bg-white/70 p-2 text-neutral-700 shadow-md backdrop-blur-sm transition hover:bg-white focus:outline-none focus:ring-1 focus:ring-neutral-400 focus:ring-offset-1"
      aria-label="Previous image"
    >
      <iconify-icon icon="tabler:chevron-left" class="text-xl"></iconify-icon>
    </button>
    <button
      v-if="images.length > 1"
      @click="nextImage"
      class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full bg-white/70 p-2 text-neutral-700 shadow-md backdrop-blur-sm transition hover:bg-white focus:outline-none focus:ring-1 focus:ring-neutral-400 focus:ring-offset-1"
      aria-label="Next image"
    >
      <iconify-icon icon="tabler:chevron-right" class="text-xl"></iconify-icon>
    </button>

    <!-- Pagination Dots -->
    <div v-if="images.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
      <button
        v-for="(image, index) in images"
        :key="`dot-${image.id}`"
        @click="goToImage(index)"
        :class="[
          'w-2 h-2 rounded-full bg-white/50 transition-colors duration-200',
          { 'bg-white': index === currentIndex, 'hover:bg-white/80': index !== currentIndex }
        ]"
        :aria-label="`Go to image ${index + 1}`"
      ></button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

interface Image {
  id: string | number;
  url: string;
  alt: string;
}

const props = defineProps<{
  images: Image[];
}>();

const currentIndex = ref(0);
const direction = ref<'left' | 'right'>('right'); // 'left' for prev, 'right' for next

const transitionName = computed(() => `slide-${direction.value}`);

const nextImage = () => {
  direction.value = 'right';
  currentIndex.value = (currentIndex.value + 1) % props.images.length;
};

const prevImage = () => {
  direction.value = 'left';
  currentIndex.value = (currentIndex.value - 1 + props.images.length) % props.images.length;
};

const goToImage = (index: number) => {
  if (index === currentIndex.value) return;
  direction.value = index > currentIndex.value ? 'right' : 'left';
  currentIndex.value = index;
};
</script>

<style scoped>
/* Common transition properties */
.slide-right-enter-active,
.slide-right-leave-active,
.slide-left-enter-active,
.slide-left-leave-active {
  transition: transform 200ms ease-out; /* Within 150ms-220ms range */
  position: absolute; /* Ensure elements are positioned for smooth transition */
  width: 100%;
  height: 100%;
}

/* Slide Right (Next Image) */
.slide-right-enter-from {
  transform: translateX(100%); /* New image enters from right */
}
.slide-right-leave-to {
  transform: translateX(-100%); /* Old image leaves to left */
}

/* Slide Left (Previous Image) */
.slide-left-enter-from {
  transform: translateX(-100%); /* New image enters from left */
}
.slide-left-leave-to {
  transform: translateX(100%); /* Old image leaves to right */
}
</style>
