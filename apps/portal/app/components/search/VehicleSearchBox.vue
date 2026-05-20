<script setup lang="ts">
import { useRouter } from '#app';
import { useVehicles } from '~/composables/useVehicles';
import { useI18n } from '~/composables/useI18n';
import UiButton from '~/components/ui/UiButton.vue';

const router = useRouter();
const { search } = useVehicles();
const { t } = useI18n();

const handleSearch = () => {
    router.push('/veiculos');
};
</script>

<template>
    <form @submit.prevent="handleSearch" class="w-full max-w-2xl mx-auto">
        <div
            class="relative bg-white border border-neutral-200 shadow-card hover:shadow-card-hover rounded-card p-2 flex items-center gap-2 transition-shadow duration-155"
        >
            <span class="pl-3.5 flex items-center text-neutral-400">
                <iconify-icon icon="tabler:search" class="text-xl"></iconify-icon>
            </span>
            <input
                v-model="search"
                type="text"
                :placeholder="t('search.placeholder')"
                class="flex-1 bg-transparent text-sm text-neutral-800 placeholder-neutral-400 outline-none h-10 px-2"
            />
            <UiButton type="submit" variant="primary" size="md" class="shadow-sm shadow-brand-500/20">
                <span>{{ t('home') }}</span>
            </UiButton>
        </div>
    </form>
</template>
