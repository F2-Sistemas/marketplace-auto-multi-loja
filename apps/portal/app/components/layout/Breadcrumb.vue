<script setup lang="ts">
import { useI18n } from '~/composables/useI18n';

interface BreadcrumbItem {
    label: string;
    to?: string;
}

defineProps<{
    items: BreadcrumbItem[];
}>();

const { t } = useI18n();
</script>

<template>
    <nav
        class="flex text-xs font-semibold text-neutral-400 select-none overflow-x-auto whitespace-nowrap py-2"
        aria-label="Breadcrumb"
    >
        <ol class="inline-flex items-center gap-1.5">
            <!-- Home Anchor -->
            <li class="inline-flex items-center">
                <NuxtLink to="/" class="hover:text-brand-600 flex items-center gap-1 transition-colors">
                    <iconify-icon icon="tabler:home" class="text-sm"></iconify-icon>
                    <span>{{ t('home') }}</span>
                </NuxtLink>
            </li>

            <!-- Path Items -->
            <li v-for="(item, index) in items" :key="index" class="inline-flex items-center gap-1.5">
                <span class="text-neutral-300">
                    <iconify-icon icon="tabler:chevron-right" class="text-xs"></iconify-icon>
                </span>

                <NuxtLink
                    v-if="item.to && index < items.length - 1"
                    :to="item.to"
                    class="hover:text-brand-600 transition-colors capitalize"
                >
                    {{ item.label }}
                </NuxtLink>
                <span v-else class="text-neutral-600 font-extrabold capitalize truncate max-w-[200px]">
                    {{ item.label }}
                </span>
            </li>
        </ol>
    </nav>
</template>
