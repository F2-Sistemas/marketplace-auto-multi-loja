<script setup lang="ts">
import { useVehicles } from '~/composables/useVehicles';
import { useI18n } from '~/composables/useI18n';
import UiSelect from '~/components/ui/UiSelect.vue';

const { sortBy } = useVehicles();
const { t } = useI18n();
</script>

<template>
    <div class="flex items-center gap-2">
        <label
            for="sort-select"
            class="text-xs font-semibold uppercase tracking-wider text-neutral-400 select-none whitespace-nowrap"
        >
            {{ t('search.sortLabel') }}
        </label>
        <div class="w-48">
            <UiSelect
                v-model="sortBy"
                :options="[
                    { label: t('search.sortNewest'), value: 'newest' },
                    { label: t('search.sortPriceAsc'), value: 'price_asc' },
                    { label: t('search.sortPriceDesc'), value: 'price_desc' },
                    { label: 'Ano mais recente', value: 'year_desc' },
                ]"
                id="sort-select"
            />
        </div>
    </div>
</template>
