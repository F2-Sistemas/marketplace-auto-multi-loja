<script setup lang="ts">
import { ref } from 'vue';
import UiCard from '~/components/ui/UiCard.vue';
import UiButton from '~/components/ui/UiButton.vue';
import UiInput from '~/components/ui/UiInput.vue';
import { useApi } from '~/composables/useApi';
import { useI18n } from '~/composables/useI18n';

definePageMeta({
    layout: 'default',
});

const { getApiUrl } = useApi();
const { t } = useI18n();

const themeForm = ref({
    accent_color: '#3b82f6', // default blue
    secondary_color: '#1e293b', // default slate
    font_family: 'Inter',
    layout_style: 'minimalist',
});

const fontOptions = ['Inter', 'Outfit', 'Roboto', 'Playfair Display'];
const layoutOptions = [
    { value: 'minimalist', label: t('theme.layout.minimalist') },
    { value: 'advanced', label: t('theme.layout.advanced') },
    { value: 'grid', label: t('theme.layout.grid') },
    { value: 'list', label: t('theme.layout.list') },
];

const saving = ref(false);
const successMessage = ref('');

const saveTheme = async () => {
    saving.value = true;
    successMessage.value = '';
    try {
        await $fetch(getApiUrl('/api/store/settings/theme'), {
            method: 'POST',
            body: themeForm.value,
        });
        successMessage.value = t('theme.success');
        setTimeout(() => {
            successMessage.value = '';
        }, 3000);
    } catch (e) {
        console.error('Erro ao salvar tema', e);
    } finally {
        saving.value = false;
    }
};
</script>

<template>
    <div class="container max-w-4xl mx-auto px-4 md:px-6 py-8 space-y-8 animate-fadeIn">
        <!-- Header -->
        <div>
            <h1 class="text-3xl font-extrabold text-slate-100 tracking-tight">{{ t('theme.title') }}</h1>
            <p class="text-slate-400 mt-2">
                {{ t('theme.subtitle') }}
            </p>
        </div>

        <!-- Feedback Message -->
        <div
            v-if="successMessage"
            class="p-4 bg-emerald-950/40 text-emerald-400 border border-emerald-800 rounded-lg flex items-center gap-2"
        >
            <iconify-icon icon="tabler:check"></iconify-icon>
            <span class="font-medium text-sm">{{ successMessage }}</span>
        </div>

        <form @submit.prevent="saveTheme" class="space-y-6">
            <UiCard class="p-6 space-y-6 border border-slate-800 bg-slate-900/40 backdrop-blur-sm">
                <div>
                    <h2
                        class="text-xl font-semibold text-slate-200 flex items-center gap-2 border-b border-slate-800 pb-2 mb-6"
                    >
                        <iconify-icon icon="tabler:palette" class="text-brand-500"></iconify-icon>
                        {{ t('theme.section.colors') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-300">
                                {{ t('theme.color.accent') }}
                            </label>
                            <div class="flex items-center gap-3">
                                <input
                                    type="color"
                                    v-model="themeForm.accent_color"
                                    class="w-12 h-12 rounded cursor-pointer border-0 p-0 bg-transparent"
                                />
                                <UiInput
                                    v-model="themeForm.accent_color"
                                    :placeholder="t('theme.color.placeholder')"
                                    class="flex-1"
                                />
                            </div>
                            <p class="text-xs text-slate-500">{{ t('theme.color.accent.hint') }}</p>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-300">
                                {{ t('theme.color.secondary') }}
                            </label>
                            <div class="flex items-center gap-3">
                                <input
                                    type="color"
                                    v-model="themeForm.secondary_color"
                                    class="w-12 h-12 rounded cursor-pointer border-0 p-0 bg-transparent"
                                />
                                <UiInput
                                    v-model="themeForm.secondary_color"
                                    :placeholder="t('theme.color.placeholder')"
                                    class="flex-1"
                                />
                            </div>
                            <p class="text-xs text-slate-500">{{ t('theme.color.secondary.hint') }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <h2
                        class="text-xl font-semibold text-slate-200 flex items-center gap-2 border-b border-slate-800 pb-2 mb-6"
                    >
                        <iconify-icon icon="tabler:typography" class="text-brand-500"></iconify-icon>
                        {{ t('theme.section.typo') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-300">
                                {{ t('theme.font.label') }}
                            </label>
                            <select
                                v-model="themeForm.font_family"
                                class="w-full h-11 px-4 bg-slate-950 border border-slate-800 text-slate-200 rounded-md focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-shadow"
                            >
                                <option v-for="font in fontOptions" :key="font" :value="font">{{ font }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-300">
                                {{ t('theme.layout.label') }}
                            </label>
                            <select
                                v-model="themeForm.layout_style"
                                class="w-full h-11 px-4 bg-slate-950 border border-slate-800 text-slate-200 rounded-md focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-shadow"
                            >
                                <option v-for="layout in layoutOptions" :key="layout.value" :value="layout.value">
                                    {{ layout.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </UiCard>

            <div class="flex justify-end">
                <UiButton
                    type="submit"
                    variant="primary"
                    :disabled="saving"
                    class="min-w-[150px] store-primary-btn border-none"
                >
                    <iconify-icon v-if="saving" icon="tabler:loader" class="animate-spin"></iconify-icon>
                    <iconify-icon v-else icon="tabler:device-floppy"></iconify-icon>
                    <span>{{ saving ? t('theme.button.saving') : t('theme.button.save') }}</span>
                </UiButton>
            </div>
        </form>
    </div>
</template>

<style scoped>
.store-primary-btn {
    background: linear-gradient(to right, var(--store-primary-from), var(--store-primary-to));
    color: var(--store-btn-text, #fff);
}
.store-primary-btn:hover {
    filter: brightness(1.1);
}
</style>
