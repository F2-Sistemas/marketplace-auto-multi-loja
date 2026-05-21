<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useTenant, type StoreLayoutStyle } from '~/composables/useTenant';
import { useApi } from '~/composables/useApi';
import { useI18n } from '~/composables/useI18n';
import UiCard from '~/components/ui/UiCard.vue';
import UiButton from '~/components/ui/UiButton.vue';
import UiInput from '~/components/ui/UiInput.vue';

definePageMeta({
    layout: 'default',
});

const { getApiUrl } = useApi();
const { t } = useI18n();
const { currentStore } = useTenant();

const themeForm = ref({
    accent_color: '#3b82f6',
    secondary_color: '#1e293b',
    font_family: 'Inter',
    layout_style: 'showroom' as StoreLayoutStyle,
});

const hasSyncedTheme = ref(false);

watch(
    currentStore,
    (store) => {
        if (!store || hasSyncedTheme.value) {
            return;
        }

        themeForm.value = {
            accent_color: store.accentColor || '#3b82f6',
            secondary_color: store.secondaryColor || '#1e293b',
            font_family: store.fontFamily || 'Inter',
            layout_style: store.layoutStyle || 'showroom',
        };
        hasSyncedTheme.value = true;
    },
    { immediate: true }
);

const fontOptions = ['Inter', 'Outfit', 'Roboto', 'Playfair Display'];
const layoutOptions: Array<{
    value: StoreLayoutStyle;
    title: string;
    description: string;
    benefits: string[];
}> = [
    {
        value: 'showroom',
        title: t('theme.layout.showroom'),
        description: t('theme.layout.showroom.description'),
        benefits: [
            t('theme.layout.showroom.benefit1'),
            t('theme.layout.showroom.benefit2'),
            t('theme.layout.showroom.benefit3'),
        ],
    },
    {
        value: 'catalog',
        title: t('theme.layout.catalog'),
        description: t('theme.layout.catalog.description'),
        benefits: [
            t('theme.layout.catalog.benefit1'),
            t('theme.layout.catalog.benefit2'),
            t('theme.layout.catalog.benefit3'),
        ],
    },
];

const saving = ref(false);
const successMessage = ref('');

const selectedLayout = computed(() => {
    return layoutOptions.find((option) => option.value === themeForm.value.layout_style) || layoutOptions[0];
});

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
    } catch (error) {
        console.error('Erro ao salvar tema', error);
    } finally {
        saving.value = false;
    }
};
</script>

<template>
    <div class="container mx-auto max-w-6xl space-y-8 px-4 py-8 md:px-6">
        <div class="grid gap-6 lg:grid-cols-[1.25fr_0.75fr]">
            <div class="space-y-4">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">
                    {{ t('theme.section.template') }}
                </p>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-100 md:text-4xl">
                    {{ t('theme.title') }}
                </h1>
                <p class="max-w-2xl text-sm leading-relaxed text-slate-400">
                    {{ t('theme.subtitle') }}
                </p>
                <p class="max-w-2xl text-sm leading-relaxed text-slate-500">
                    {{ t('theme.template.helper') }}
                </p>
            </div>

            <UiCard class="border border-slate-800 bg-slate-900/50 p-5">
                <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-slate-500">
                    {{ t('theme.template.label') }}
                </p>
                <div class="mt-3 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-200">{{ selectedLayout.title }}</p>
                        <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ selectedLayout.description }}</p>
                    </div>
                    <span
                        class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-emerald-400"
                    >
                        {{ t('theme.layout.selected') }}
                    </span>
                </div>
            </UiCard>
        </div>

        <div
            v-if="successMessage"
            class="flex items-center gap-2 rounded-xl border border-emerald-800 bg-emerald-950/40 p-4 text-emerald-400"
        >
            <iconify-icon icon="tabler:check" />
            <span class="text-sm font-medium">{{ successMessage }}</span>
        </div>

        <form @submit.prevent="saveTheme" class="space-y-6">
            <UiCard class="space-y-8 border border-slate-800 bg-slate-900/40 p-6 backdrop-blur-sm">
                <section class="space-y-5">
                    <div class="space-y-2">
                        <h2 class="flex items-center gap-2 border-b border-slate-800 pb-3 text-xl font-semibold text-slate-200">
                            <iconify-icon icon="tabler:layout-grid" class="text-brand-500" />
                            {{ t('theme.layout.label') }}
                        </h2>
                        <p class="text-sm leading-relaxed text-slate-500">
                            {{ t('theme.template.helper') }}
                        </p>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-2">
                        <label
                            v-for="layout in layoutOptions"
                            :key="layout.value"
                            :class="[
                                'group relative block cursor-pointer rounded-2xl border p-4 transition duration-200',
                                {
                                    'border-brand-500 bg-brand-500/5 ring-1 ring-brand-500/25':
                                        themeForm.layout_style === layout.value,
                                    'border-slate-800 bg-slate-950/30 hover:border-slate-700 hover:bg-slate-950/50':
                                        themeForm.layout_style !== layout.value,
                                },
                            ]"
                        >
                            <input
                                v-model="themeForm.layout_style"
                                :value="layout.value"
                                type="radio"
                                class="sr-only"
                            />

                            <div class="space-y-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base font-semibold text-slate-100">
                                                {{ layout.title }}
                                            </span>
                                            <span
                                                class="rounded-full border border-slate-700 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400"
                                            >
                                                {{ layout.value }}
                                            </span>
                                        </div>
                                        <p class="max-w-md text-sm leading-relaxed text-slate-400">
                                            {{ layout.description }}
                                        </p>
                                    </div>

                                    <span
                                        :class="[
                                            'mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full border transition',
                                            {
                                                'border-brand-500 bg-brand-500 text-white':
                                                    themeForm.layout_style === layout.value,
                                                'border-slate-700 bg-slate-950 text-transparent':
                                                    themeForm.layout_style !== layout.value,
                                            },
                                        ]"
                                    >
                                        <iconify-icon icon="tabler:check" class="text-[10px]" />
                                    </span>
                                </div>

                                <div
                                    :class="[
                                        'rounded-2xl border p-4',
                                        {
                                            'border-brand-500/30 bg-linear-to-br from-slate-950 to-slate-900':
                                                layout.value === 'showroom',
                                            'border-slate-700 bg-linear-to-br from-white to-slate-100':
                                                layout.value === 'catalog',
                                        },
                                    ]"
                                >
                                    <div class="space-y-3">
                                        <div
                                            :class="[
                                                'h-20 rounded-xl',
                                                {
                                                    'bg-linear-to-r from-slate-800 via-slate-700 to-slate-800':
                                                        layout.value === 'showroom',
                                                    'bg-white border border-slate-200':
                                                        layout.value === 'catalog',
                                                },
                                            ]"
                                        >
                                            <div
                                                :class="[
                                                    'h-8 rounded-t-xl',
                                                    {
                                                        'bg-linear-to-r from-brand-500 to-amber-500':
                                                            layout.value === 'showroom',
                                                        'bg-slate-200': layout.value === 'catalog',
                                                    },
                                                ]"
                                            ></div>
                                            <div class="flex gap-2 px-3 py-2">
                                                <span
                                                    :class="[
                                                        'h-2 rounded-full',
                                                        layout.value === 'showroom' ? 'w-16 bg-slate-500/70' : 'w-12 bg-slate-300',
                                                    ]"
                                                ></span>
                                                <span
                                                    :class="[
                                                        'h-2 rounded-full',
                                                        layout.value === 'showroom' ? 'w-10 bg-slate-600/70' : 'w-20 bg-slate-300',
                                                    ]"
                                                ></span>
                                                <span
                                                    :class="[
                                                        'h-2 rounded-full',
                                                        layout.value === 'showroom' ? 'w-14 bg-slate-700/80' : 'w-16 bg-slate-300',
                                                    ]"
                                                ></span>
                                            </div>
                                        </div>

                                        <div class="grid gap-2 sm:grid-cols-3">
                                            <span
                                                v-for="benefit in layout.benefits"
                                                :key="benefit"
                                                :class="[
                                                    'rounded-xl px-3 py-2 text-[11px] font-medium leading-snug',
                                                    {
                                                        'bg-slate-900/80 text-slate-300': layout.value === 'showroom',
                                                        'bg-white text-slate-600 border border-slate-200':
                                                            layout.value === 'catalog',
                                                    },
                                                ]"
                                            >
                                                {{ benefit }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </section>

                <section class="space-y-6 pt-2">
                    <h2 class="flex items-center gap-2 border-b border-slate-800 pb-3 text-xl font-semibold text-slate-200">
                        <iconify-icon icon="tabler:palette" class="text-brand-500" />
                        {{ t('theme.section.colors') }}
                    </h2>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-300">
                                {{ t('theme.color.accent') }}
                            </label>
                            <div class="flex items-center gap-3">
                                <input
                                    v-model="themeForm.accent_color"
                                    type="color"
                                    class="h-12 w-12 cursor-pointer rounded border-0 bg-transparent p-0"
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
                                    v-model="themeForm.secondary_color"
                                    type="color"
                                    class="h-12 w-12 cursor-pointer rounded border-0 bg-transparent p-0"
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
                </section>

                <section class="space-y-6 pt-2">
                    <h2 class="flex items-center gap-2 border-b border-slate-800 pb-3 text-xl font-semibold text-slate-200">
                        <iconify-icon icon="tabler:typography" class="text-brand-500" />
                        {{ t('theme.section.typo') }}
                    </h2>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-300">
                                {{ t('theme.font.label') }}
                            </label>
                            <select
                                v-model="themeForm.font_family"
                                class="h-11 w-full rounded-md border border-slate-800 bg-slate-950 px-4 text-slate-200 transition-shadow focus:border-brand-500 focus:ring-2 focus:ring-brand-500"
                            >
                                <option v-for="font in fontOptions" :key="font" :value="font">
                                    {{ font }}
                                </option>
                            </select>
                        </div>

                        <UiCard class="border border-slate-800 bg-slate-950/40 p-4">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-slate-500">
                                {{ t('theme.layout.label') }}
                            </p>
                            <p class="mt-2 text-sm font-semibold text-slate-200">
                                {{ selectedLayout.title }}
                            </p>
                            <p class="mt-2 text-xs leading-relaxed text-slate-500">
                                {{ selectedLayout.description }}
                            </p>
                        </UiCard>
                    </div>
                </section>
            </UiCard>

            <div class="flex justify-end">
                <UiButton
                    :disabled="saving"
                    type="submit"
                    variant="primary"
                    class="min-w-[150px] border-none store-primary-btn"
                >
                    <iconify-icon v-if="saving" icon="tabler:loader" class="animate-spin" />
                    <iconify-icon v-else icon="tabler:device-floppy" />
                    <span>{{ saving ? t('theme.button.saving') : t('theme.button.save') }}</span>
                </UiButton>
            </div>
        </form>
    </div>
</template>

<style scoped>
.store-primary-btn {
    background: linear-gradient(to right, var(--store-primary-from), var(--store-primary-to));
    color: var(--store-btn-text, #ffffff);
}

.store-primary-btn:hover {
    filter: brightness(1.08);
}
</style>
