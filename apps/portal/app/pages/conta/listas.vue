<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useI18n } from '~/composables/useI18n';
import { useInterestLists } from '~/composables/useInterestLists';
import Breadcrumb from '~/components/layout/Breadcrumb.vue';
import SeoPageHeader from '~/components/layout/SeoPageHeader.vue';
import UiButton from '~/components/ui/UiButton.vue';
import UiCard from '~/components/ui/UiCard.vue';

definePageMeta({
    layout: 'default',
});

const { t } = useI18n();
const {
    interestLists,
    fetchInterestLists,
    createInterestList,
    updateInterestList,
} = useInterestLists();

const name = ref('');
const description = ref('');
const isPublic = ref(true);
const isSaving = ref(false);
const editingListId = ref<number | null>(null);

const sortedLists = computed(() => {
    return [...interestLists.value].sort((left, right) => {
        return right.id - left.id;
    });
});

const loadLists = async () => {
    await fetchInterestLists().catch(() => undefined);
};

const resetForm = () => {
    name.value = '';
    description.value = '';
    isPublic.value = true;
    editingListId.value = null;
};

const submitForm = async () => {
    if (!name.value.trim()) {
        return;
    }

    isSaving.value = true;

    try {
        if (editingListId.value === null) {
            await createInterestList({
                name: name.value,
                description: description.value,
                is_public: isPublic.value,
            });
        } else {
            await updateInterestList(editingListId.value, {
                name: name.value,
                description: description.value,
                is_public: isPublic.value,
            });
        }

        resetForm();
    } finally {
        isSaving.value = false;
    }
};

const startEditing = (list: {
    id: number;
    name: string;
    description?: string | null;
    is_public: boolean;
}) => {
    editingListId.value = list.id;
    name.value = list.name;
    description.value = list.description || '';
    isPublic.value = list.is_public;
};

onMounted(async () => {
    await loadLists();
});
</script>

<template>
    <div class="container mx-auto max-w-7xl space-y-6 px-4 py-6 md:px-6">
        <Breadcrumb
            :items="[
                { label: t('favorites.breadcrumbAccount'), to: '/conta' },
                { label: t('interestLists.title') },
            ]"
        />

        <SeoPageHeader
            :title="t('interestLists.title')"
            :description="t('interestLists.description')"
        />

        <div class="grid gap-6 lg:grid-cols-[380px_minmax(0,1fr)]">
            <UiCard body-class="space-y-4 p-5">
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-neutral-800">
                        {{ t('interestLists.formTitle') }}
                    </h3>
                    <p class="text-sm text-neutral-500">
                        {{ t('interestLists.formDescription') }}
                    </p>
                </div>

                <div class="space-y-3">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold uppercase tracking-wider text-neutral-400">
                            {{ t('interestLists.nameLabel') }}
                        </label>
                        <input
                            v-model="name"
                            type="text"
                            class="w-full rounded-input border border-neutral-200 bg-white px-4 py-3 text-sm text-neutral-700 outline-none transition focus:border-neutral-300"
                            :placeholder="t('interestLists.nameLabel')"
                        />
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold uppercase tracking-wider text-neutral-400">
                            {{ t('interestLists.descriptionLabel') }}
                        </label>
                        <textarea
                            v-model="description"
                            rows="4"
                            class="w-full rounded-input border border-neutral-200 bg-white px-4 py-3 text-sm text-neutral-700 outline-none transition focus:border-neutral-300"
                            :placeholder="t('interestLists.descriptionLabel')"
                        ></textarea>
                    </div>

                    <label class="flex items-center gap-2 text-sm font-medium text-neutral-600">
                        <input v-model="isPublic" type="checkbox" class="rounded border-neutral-300" />
                        {{ t('interestLists.publicToggleLabel') }}
                    </label>
                </div>

                <div class="flex gap-2">
                    <UiButton variant="primary" size="md" class="flex-1" :loading="isSaving" @click="submitForm">
                        {{ editingListId === null ? t('interestLists.createButton') : t('interestLists.saveButton') }}
                    </UiButton>
                    <UiButton variant="outline" size="md" @click="resetForm">
                        {{ t('interestLists.clearButton') }}
                    </UiButton>
                </div>
            </UiCard>

            <div class="space-y-4">
                <div class="flex items-center justify-between rounded-card border border-neutral-200 bg-white p-4 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-neutral-500">
                        {{ t('interestLists.count', { count: sortedLists.length }) }}
                    </div>

                    <UiButton variant="outline" size="sm" @click="loadLists">
                        <iconify-icon icon="tabler:refresh" class="text-sm"></iconify-icon>
                        {{ t('interestLists.reload') }}
                    </UiButton>
                </div>

                <div
                    v-if="sortedLists.length === 0"
                    class="rounded-card border border-dashed border-neutral-200 bg-white p-10 text-center text-sm text-neutral-500"
                >
                    {{ t('interestLists.empty') }}
                </div>

                <div v-else class="grid gap-4 md:grid-cols-2">
                    <UiCard v-for="list in sortedLists" :key="list.id" body-class="space-y-4 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 space-y-1">
                                <h4 class="truncate text-base font-bold text-neutral-800">{{ list.name }}</h4>
                                <p class="line-clamp-2 text-sm text-neutral-500">
                                    {{ list.description || t('interestLists.noDescription') }}
                                </p>
                            </div>

                                <span
                                    class="shrink-0 rounded-full px-2 py-1 text-[10px] font-semibold uppercase tracking-wider"
                                    :class="[
                                        {
                                            'bg-emerald-50 text-emerald-700': list.is_public,
                                        'bg-neutral-100 text-neutral-500': !list.is_public,
                                    },
                                ]"
                            >
                                {{ list.is_public ? t('interestLists.publicBadge') : t('interestLists.privateBadge') }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 text-xs text-neutral-500">
                            <span>{{ t('interestLists.vehiclesCount', { count: list.items_count }) }}</span>
                            <span v-if="list.public_url && list.is_public">•</span>
                            <NuxtLink
                                v-if="list.public_url && list.is_public"
                                :to="list.public_url"
                                class="font-semibold text-brand-600 transition hover:text-brand-700"
                            >
                                {{ t('interestLists.publicLink') }}
                            </NuxtLink>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <UiButton variant="outline" size="sm" @click="startEditing(list)">
                                {{ t('interestLists.editButton') }}
                            </UiButton>
                            <UiButton
                                v-if="list.public_url && list.is_public"
                                variant="secondary"
                                size="sm"
                                :href="list.public_url"
                            >
                                {{ t('interestLists.share') }}
                            </UiButton>
                        </div>
                    </UiCard>
                </div>
            </div>
        </div>
    </div>
</template>
