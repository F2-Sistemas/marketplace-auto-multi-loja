<script setup lang="ts">
interface Store {
  id: number;
  name: string;
  slug: string;
  logo?: string;
  description?: string;
  city?: string;
  state?: string;
}

defineProps<{
  store: Store;
}>();
</script>

<template>
  <div class="relative bg-neutral-900 rounded-card overflow-hidden text-white shadow-card">
    <!-- Big cover visual with overlay gradient -->
    <div class="h-44 md:h-60 bg-gradient-to-r from-brand-600 to-indigo-800 relative overflow-hidden">
      <!-- Grid patterns or abstract overlay -->
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent"></div>
    </div>

    <!-- Info Block on bottom -->
    <div class="px-6 pb-6 pt-16 md:pt-6 flex flex-col md:flex-row md:items-end justify-between gap-4 relative">
      <!-- Floating Logo -->
      <div class="absolute -top-12 left-6 w-24 h-24 rounded-2xl bg-white border-4 border-white shadow-lg overflow-hidden flex items-center justify-center">
        <img
          v-if="store.logo"
          :src="store.logo"
          :alt="store.name"
          class="w-full h-full object-cover"
        />
        <iconify-icon v-else icon="tabler:building-store" class="text-neutral-400 text-4xl"></iconify-icon>
      </div>

      <div class="space-y-1">
        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-neutral-800">
          {{ store.name }}
        </h1>
        <div class="flex items-center gap-1.5 text-xs font-bold text-neutral-400 uppercase tracking-wider">
          <iconify-icon icon="tabler:map-pin" class="text-sm"></iconify-icon>
          <span>{{ store.city || 'São Paulo' }} - {{ store.state || 'SP' }}</span>
        </div>
      </div>

      <div class="text-xs font-extrabold px-3 py-1.5 bg-brand-50 text-brand-700 rounded-full border border-brand-100 uppercase tracking-wider self-start md:self-end">
        Parceiro Oficial Marketplace
      </div>
    </div>
  </div>
</template>
