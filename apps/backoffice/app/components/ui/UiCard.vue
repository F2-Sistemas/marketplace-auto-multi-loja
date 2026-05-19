<script setup lang="ts">
interface Props {
  title?: string;
  desc?: string;
  icon?: string;
  iconColor?: string;
  overflowHidden?: boolean;
}

withDefaults(defineProps<Props>(), {
  overflowHidden: true,
});
</script>

<template>
  <div
    :class="[
      'bg-slate-900 border border-slate-800 rounded-2xl p-6 relative',
      overflowHidden ? 'overflow-hidden' : ''
    ]"
  >
    <!-- Background Icon Decoration -->
    <div
      v-if="icon && !title"
      class="absolute -right-4 -bottom-4 text-slate-800/20 text-8xl font-bold select-none pointer-events-none"
    >
      <iconify-icon :icon="icon"></iconify-icon>
    </div>

    <!-- Optional Header Slot/Props -->
    <div v-if="title || $slots.header" class="mb-4 flex items-center justify-between">
      <slot name="header">
        <h3 class="text-base font-bold text-white flex items-center gap-2">
          <iconify-icon
            v-if="icon"
            :icon="icon"
            :class="iconColor || 'text-indigo-400'"
          ></iconify-icon>
          <span>{{ title }}</span>
        </h3>
      </slot>
    </div>
    
    <p v-if="desc" class="text-xs text-slate-500 leading-relaxed mb-4">
      {{ desc }}
    </p>

    <!-- Content Slot -->
    <slot></slot>
  </div>
</template>
