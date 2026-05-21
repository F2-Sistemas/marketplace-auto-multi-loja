<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useRequestURL } from '#app';
import { useI18n } from '~/composables/useI18n';
import { useInterestLists } from '~/composables/useInterestLists';
import {
    buildVehicleShareActions,
    buildVehicleWhatsAppUrl,
} from '~/utils/vehicleSharing';
import UiButton from '~/components/ui/UiButton.vue';
import UiModal from '~/components/ui/UiModal.vue';
import UiInput from '~/components/ui/UiInput.vue';
import UiCard from '~/components/ui/UiCard.vue';

interface VehicleStore {
    name: string;
    slug: string;
    whatsapp_number?: string;
}

interface Vehicle {
    id: number;
    title: string;
    slug: string;
    store?: VehicleStore;
}

const props = defineProps<{
    vehicle: Vehicle;
}>();

const { t } = useI18n();
const requestUrl = useRequestURL();
const {
    interestLists,
    fetchInterestLists,
    createInterestList,
    addVehicleToList,
} = useInterestLists();

const isInterestModalOpen = ref(false);
const isComposerOpen = ref(false);
const isCreatingList = ref(false);
const isAddingToListId = ref<number | null>(null);
const shareFeedback = ref('');

const listName = ref('');
const listDescription = ref('');
const listIsPublic = ref(true);
const composerMessage = ref(t('sharing.whatsappDraft', { title: props.vehicle.title }));

const shareUrl = computed(() => {
    return `${requestUrl.origin}/veiculos/${props.vehicle.slug}`;
});

const shareActions = computed(() => {
    return buildVehicleShareActions(props.vehicle.title, shareUrl.value);
});

const openInterestModal = async () => {
    shareFeedback.value = '';
    isInterestModalOpen.value = true;

    if (interestLists.value.length === 0) {
        await fetchInterestLists().catch(() => undefined);
    }
};

const closeInterestModal = () => {
    isInterestModalOpen.value = false;
};

const openComposer = () => {
    composerMessage.value = t('sharing.whatsappDraft', { title: props.vehicle.title });
    isComposerOpen.value = true;
};

const closeComposer = () => {
    isComposerOpen.value = false;
};

const shareAction = async (key: string) => {
    const action = shareActions.value.find((item) => item.key === key);
    if (!action) {
        return;
    }

    shareFeedback.value = '';

    if (action.key === 'instagram') {
        await navigator.clipboard.writeText(action.copyText || '').catch(() => undefined);
        shareFeedback.value = t('sharing.instagramCopied');
        window.open('https://www.instagram.com/', '_blank', 'noopener,noreferrer');
        return;
    }

    if (action.key === 'copy') {
        await navigator.clipboard.writeText(action.copyText || '').catch(() => undefined);
        shareFeedback.value = t('sharing.linkCopied');
        return;
    }

    if (action.href) {
        window.open(action.href, '_blank', 'noopener,noreferrer');
    }
};

const createAndAddToList = async () => {
    if (!listName.value.trim()) {
        return;
    }

    isCreatingList.value = true;

    try {
        const response = await createInterestList({
            name: listName.value,
            description: listDescription.value,
            is_public: listIsPublic.value,
        });

        if (response.list_id) {
            await addVehicleToList(response.list_id, props.vehicle.id);
        }

        listName.value = '';
        listDescription.value = '';
        listIsPublic.value = true;
        closeInterestModal();
        shareFeedback.value = t('sharing.listCreatedSuccess');
    } catch {
        shareFeedback.value = t('sharing.createListError');
    } finally {
        isCreatingList.value = false;
    }
};

const addCurrentVehicleToList = async (listId: number) => {
    isAddingToListId.value = listId;

    try {
        await addVehicleToList(listId, props.vehicle.id);
        shareFeedback.value = t('sharing.addedToListSuccess');
        closeInterestModal();
    } catch {
        shareFeedback.value = t('sharing.addToListError');
    } finally {
        isAddingToListId.value = null;
    }
};

const sendWhatsappMessage = () => {
    if (!props.vehicle.store || !props.vehicle.store.whatsapp_number) {
        return;
    }

    const url = buildVehicleWhatsAppUrl(props.vehicle.store.whatsapp_number, composerMessage.value);
    window.open(url, '_blank', 'noopener,noreferrer');
    closeComposer();
};

onMounted(() => {
    if (interestLists.value.length === 0) {
        void fetchInterestLists().catch(() => undefined);
    }
});
</script>

<template>
    <section class="space-y-4 rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div class="space-y-1">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-500">
                    {{ t('sharing.title') }}
                </h3>
                <p class="text-xs text-neutral-400">
                    {{ t('sharing.subtitle') }}
                </p>
            </div>

            <UiButton variant="soft" size="sm" @click="openComposer">
                <template #icon>
                    <iconify-icon icon="tabler:brand-whatsapp" class="text-sm"></iconify-icon>
                </template>
                {{ t('sharing.whatsapp') }}
            </UiButton>
        </div>

        <div class="grid grid-cols-5 gap-2">
            <button
                v-for="action in shareActions"
                :key="action.key"
                type="button"
                class="flex flex-col items-center gap-2 rounded-xl border border-neutral-200 bg-neutral-50 px-2 py-3 text-center transition hover:border-neutral-300 hover:bg-neutral-100"
                @click="shareAction(action.key)"
            >
                <span
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-neutral-700 shadow-sm"
                >
                    <iconify-icon
                        :icon="
                            action.key === 'whatsapp'
                                ? 'tabler:brand-whatsapp'
                                : action.key === 'x'
                                    ? 'tabler:brand-x'
                                    : action.key === 'instagram'
                                        ? 'tabler:brand-instagram'
                                        : action.key === 'email'
                                            ? 'tabler:mail'
                                            : 'tabler:copy'
                        "
                        class="text-lg"
                    ></iconify-icon>
                </span>
                <span class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">
                    {{ t(action.labelKey) }}
                </span>
            </button>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row">
            <UiButton variant="outline" size="md" class="w-full sm:w-auto" @click="openInterestModal">
                <template #icon>
                    <iconify-icon icon="tabler:playlist-add" class="text-base"></iconify-icon>
                </template>
                {{ t('sharing.addToList') }}
            </UiButton>

            <UiButton
                v-if="props.vehicle.store && props.vehicle.store.whatsapp_number"
                variant="secondary"
                size="md"
                class="w-full sm:w-auto"
                @click="openComposer"
            >
                <template #icon>
                    <iconify-icon icon="tabler:message-circle-2" class="text-base"></iconify-icon>
                </template>
                {{ t('sharing.formMessage') }}
            </UiButton>
        </div>

        <p v-if="shareFeedback" class="text-xs font-medium text-emerald-600">
            {{ shareFeedback }}
        </p>
    </section>

    <Teleport to="body">
        <div
            v-if="isComposerOpen"
            class="fixed bottom-4 right-4 z-50 w-[calc(100vw-2rem)] max-w-[420px] overflow-hidden rounded-[28px] border border-neutral-200 bg-white shadow-2xl"
        >
            <div class="flex items-center justify-between gap-3 bg-emerald-700 px-5 py-4 text-white">
                <div class="space-y-0.5">
                    <h3 class="text-lg font-semibold">{{ t('sharing.composerTitle') }}</h3>
                    <p class="text-sm text-emerald-50/90">
                        {{ t('sharing.composerDescription') }}
                    </p>
                </div>

                <button
                    type="button"
                    class="rounded-full p-2 transition hover:bg-white/10"
                    aria-label="Fechar"
                    @click="closeComposer"
                >
                    <iconify-icon icon="tabler:x" class="text-xl"></iconify-icon>
                </button>
            </div>

            <div class="space-y-4 bg-[linear-gradient(180deg,#f3f5f4_0%,#ffffff_100%)] px-5 py-5">
                <div class="rounded-2xl bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">
                        {{ t('sharing.composerMessageLabel') }}
                    </p>
                    <textarea
                        v-model="composerMessage"
                        rows="5"
                        class="mt-2 w-full resize-none rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-700 outline-none transition focus:border-emerald-300 focus:bg-white"
                    ></textarea>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs text-neutral-500">
                        {{ t('sharing.composerHint') }}
                    </p>

                    <button
                        type="button"
                        class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-emerald-600 text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-700"
                        aria-label="Enviar mensagem"
                        @click="sendWhatsappMessage"
                    >
                        <iconify-icon icon="tabler:send" class="text-xl"></iconify-icon>
                    </button>
                </div>
            </div>
        </div>
    </Teleport>

    <UiModal :isOpen="isInterestModalOpen" maxWidth="xl" @close="closeInterestModal">
        <div class="space-y-5 p-6">
            <div class="space-y-2">
                <h3 class="text-xl font-black tracking-tight text-neutral-800">
                    {{ t('sharing.createListTitle') }}
                </h3>
                <p class="text-sm text-neutral-500">
                    {{ t('sharing.createListDescription') }}
                </p>
            </div>

            <div class="grid gap-3 md:grid-cols-2">
                <div class="space-y-3 rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-neutral-500">
                        {{ t('sharing.createNewList') }}
                    </h4>

                    <UiInput v-model="listName" :placeholder="t('sharing.nameLabel')" />
                    <textarea
                        v-model="listDescription"
                        rows="3"
                        class="w-full rounded-input border border-neutral-200 bg-white px-4 py-3 text-sm text-neutral-700 outline-none transition focus:border-neutral-300 focus:ring-0"
                        :placeholder="t('sharing.descriptionLabel')"
                    ></textarea>

                    <label class="flex items-center gap-2 text-sm font-medium text-neutral-600">
                        <input v-model="listIsPublic" type="checkbox" class="rounded border-neutral-300" />
                        {{ t('sharing.publicListLabel') }}
                    </label>

                    <UiButton
                        variant="primary"
                        size="md"
                        class="w-full"
                        :loading="isCreatingList"
                        @click="createAndAddToList"
                    >
                        {{ t('sharing.createAndAdd') }}
                    </UiButton>
                </div>

                <div class="space-y-3 rounded-2xl border border-neutral-200 bg-white p-4">
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-neutral-500">
                        {{ t('sharing.existingLists') }}
                    </h4>

                    <div v-if="interestLists.length === 0" class="rounded-2xl border border-dashed border-neutral-200 p-4 text-sm text-neutral-500">
                        {{ t('sharing.noLists') }}
                    </div>

                    <div v-else class="space-y-3">
                        <UiCard
                            v-for="list in interestLists"
                            :key="list.id"
                            body-class="p-4 space-y-3"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h5 class="truncate text-sm font-semibold text-neutral-800">
                                        {{ list.name }}
                                    </h5>
                                    <p class="line-clamp-2 text-xs text-neutral-500">
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
                                {{ list.is_public ? t('sharing.publicBadge') : t('sharing.privateBadge') }}
                            </span>
                            </div>

                            <div class="flex flex-wrap gap-2">
                            <UiButton
                                variant="secondary"
                                size="sm"
                                :loading="isAddingToListId === list.id"
                                @click="addCurrentVehicleToList(list.id)"
                            >
                                {{ t('sharing.addHere') }}
                            </UiButton>

                            <UiButton
                                v-if="list.is_public && list.public_url"
                                variant="outline"
                                size="sm"
                                :href="list.public_url"
                            >
                                {{ t('sharing.openLink') }}
                            </UiButton>
                            </div>
                        </UiCard>
                    </div>
                </div>
            </div>
        </div>
    </UiModal>
</template>
