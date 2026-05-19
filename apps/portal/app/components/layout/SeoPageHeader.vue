<script setup lang="ts">
defineProps<{
  title: string;
  description?: string;
}>();
</script>

<template>
  <div class="space-y-2 py-4">
    <h1 class="text-3xl font-black text-neutral-800 tracking-tight leading-tight md:text-4xl">
      {{ title }}
    </h1>
    <p v-if="description" class="text-sm font-semibold text-neutral-400 max-w-2xl leading-relaxed">
      {{ description }}
    </p>
    <div class="h-1 w-12 bg-brand-500 rounded-full mt-3"></div>
  </div>
</template>
