<script setup lang="ts">
import { computed, ref } from 'vue';
import { useRouter } from '#app';
import { useI18n } from '~/composables/useI18n';
import { useApi } from '~/composables/useApi';
import UiButton from '~/components/ui/UiButton.vue';

interface Store {
    name: string;
    slug: string;
    logo?: string;
    whatsapp_number?: string;
    status?: string;
    created_at?: string;
}

interface Vehicle {
    id: number;
    store?: Store;
    location_label?: string;
}

const props = defineProps<{
    vehicle: Vehicle;
    sameStoreCount: number;
}>();

const router = useRouter();
const { t } = useI18n();
const { getApiUrl } = useApi();
const { handleImageError } = useImageFallback();

const isPhoneVisible = ref(false);
const hasTrackedPhoneView = ref(false);
const isTrackingPhoneView = ref(false);

const store = computed(() => {
    return props.vehicle.store || null;
});

const isStoreActive = computed(() => {
    if (!store.value) {
        return false;
    }

    return store.value.status === 'active';
});

const storeSince = computed(() => {
    if (!store.value || !store.value.created_at) {
        return t('vehicle.seller.sinceFallback');
    }

    const createdAt = new Date(store.value.created_at);
    if (Number.isNaN(createdAt.getTime())) {
        return t('vehicle.seller.sinceFallback');
    }

    return createdAt.toLocaleDateString('pt-BR', {
        month: '2-digit',
        year: 'numeric',
    });
});

const formattedPhone = computed(() => {
    if (!store.value || !store.value.whatsapp_number) {
        return t('vehicle.seller.phoneFallback');
    }

    let digits = store.value.whatsapp_number.replace(/\D/g, '');
    if (digits.length > 11 && digits.startsWith('55')) {
        digits = digits.slice(2);
    }

    if (digits.length < 10) {
        return store.value.whatsapp_number;
    }

    const areaCode = digits.slice(0, 2);
    const localPart = digits.slice(2);

    if (localPart.length === 8) {
        return `(${areaCode}) ${localPart.slice(0, 4)}-${localPart.slice(4)}`;
    }

    if (localPart.length >= 9) {
        return `(${areaCode}) ${localPart.slice(0, 5)}-${localPart.slice(5, 9)}`;
    }

    return store.value.whatsapp_number;
});

const maskedPhone = computed(() => {
    if (!store.value || !store.value.whatsapp_number) {
        return t('vehicle.seller.phoneFallback');
    }

    let digits = store.value.whatsapp_number.replace(/\D/g, '');

    if (digits.length > 11 && digits.startsWith('55')) {
        digits = digits.slice(2);
    }

    if (digits.length === 11) {
        return `(${digits.slice(0, 2)}) ${digits.slice(2, 3)}****-****`;
    }

    if (digits.length === 10) {
        return `(${digits.slice(0, 2)}) ****-****`;
    }

    return t('vehicle.seller.phoneMaskedFallback');
});

const visiblePhone = computed(() => {
    if (isPhoneVisible.value) {
        return formattedPhone.value;
    }

    return maskedPhone.value;
});

const contactCode = computed(() => {
    return String(props.vehicle.id).padStart(4, '0');
});

const storeVehicleCount = computed(() => {
    if (props.sameStoreCount > 0) {
        return props.sameStoreCount;
    }

    return 1;
});

const handleStoreNavigate = () => {
    if (!store.value) {
        return;
    }

    router.push(`/lojas/${store.value.slug}`);
};

const trackPhoneView = async () => {
    if (!store.value) {
        return;
    }

    if (isTrackingPhoneView.value) {
        return;
    }

    isTrackingPhoneView.value = true;

    try {
        await $fetch(getApiUrl(`/api/vehicles/${props.vehicle.id}/phone-view`), {
            method: 'POST',
            body: {
                vehicle_id: props.vehicle.id,
            },
        });

        hasTrackedPhoneView.value = true;
    } catch {
        // Fire-and-forget telemetry. The interaction must never fail because of tracking.
    } finally {
        isTrackingPhoneView.value = false;
    }
};

const handlePhoneAction = () => {
    if (!isPhoneVisible.value) {
        isPhoneVisible.value = true;

        if (!hasTrackedPhoneView.value) {
            void trackPhoneView();
        }

        return;
    }

    isPhoneVisible.value = false;
};
</script>

<template>
    <section class="space-y-4">
        <div class="space-y-1">
            <span class="text-sm font-medium text-neutral-400">
                {{ t('vehicle.seller.title') }}
            </span>
            <div
                class="group/response relative inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700"
            >
                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                <span>{{ t('vehicle.seller.responseBadge') }}</span>
                <iconify-icon icon="tabler:info-circle" class="text-sm text-emerald-600"></iconify-icon>

                <div
                    class="pointer-events-none absolute left-0 top-full z-20 mt-2 hidden w-64 rounded-2xl bg-neutral-900 px-4 py-3 text-xs font-medium leading-relaxed text-white shadow-2xl group-hover/response:block"
                >
                    {{ t('vehicle.seller.responseTooltip') }}
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <div class="flex min-w-0 items-start gap-3">
                <div
                    class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-neutral-200 bg-neutral-100 shadow-sm"
                >
                    <img
                        v-if="store && store.logo"
                        :src="store.logo"
                        :alt="store.name"
                        @error="handleImageError($event, store.name)"
                        class="h-full w-full object-cover"
                    />
                    <iconify-icon
                        v-else
                        icon="tabler:building-store"
                        class="text-3xl text-neutral-400"
                    ></iconify-icon>
                </div>

                <div class="min-w-0 space-y-1">
                    <h3 class="truncate text-2xl font-semibold tracking-tight text-neutral-800">
                        {{ store ? store.name : t('vehicle.seller.storeFallback') }}
                    </h3>

                    <div class="flex items-center gap-1.5 text-sm text-neutral-500">
                        <iconify-icon icon="tabler:map-pin" class="text-base text-neutral-400"></iconify-icon>
                        <span class="truncate">
                            {{ props.vehicle.location_label || t('vehicle.seller.locationFallback') }}
                        </span>
                    </div>
                </div>
            </div>

            <div
                class="flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-3 py-2 text-sm font-semibold text-neutral-700 shadow-sm"
            >
                <span
                    :class="[
                        'h-2.5 w-2.5 rounded-full',
                        {
                            'bg-emerald-500': isStoreActive,
                            'bg-amber-500': !isStoreActive,
                        },
                    ]"
                ></span>
                <span>
                    {{ isStoreActive ? t('vehicle.seller.openNow') : t('vehicle.seller.closedNow') }}
                </span>
            </div>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-neutral-50/80 px-4 py-4 shadow-sm">
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <span
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-neutral-500 shadow-sm"
                    >
                        <iconify-icon icon="tabler:calendar-event" class="text-xl"></iconify-icon>
                    </span>
                    <div class="min-w-0">
                        <span class="block text-[10px] font-semibold uppercase tracking-wider text-neutral-400">
                            {{ t('vehicle.seller.sinceLabel') }}
                        </span>
                        <span class="block text-sm font-semibold text-neutral-700">
                            {{ t('vehicle.seller.sinceValue', { value: storeSince }) }}
                        </span>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-neutral-500 shadow-sm"
                    >
                        <iconify-icon icon="tabler:car" class="text-xl"></iconify-icon>
                    </span>
                    <div class="min-w-0">
                        <span class="block text-[10px] font-semibold uppercase tracking-wider text-neutral-400">
                            {{ t('vehicle.seller.listingsLabel') }}
                        </span>
                        <span class="block text-sm font-semibold text-neutral-700">
                            {{ t('vehicle.seller.listingsValue', { count: storeVehicleCount }) }}
                        </span>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-neutral-500 shadow-sm"
                    >
                        <iconify-icon icon="tabler:clock-check" class="text-xl"></iconify-icon>
                    </span>
                    <div class="min-w-0">
                        <span class="block text-[10px] font-semibold uppercase tracking-wider text-neutral-400">
                            {{ t('vehicle.seller.responseLabel') }}
                        </span>
                        <span class="block text-sm font-semibold text-neutral-700">
                            {{ t('vehicle.seller.responseValue') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-3 rounded-2xl border border-neutral-200 bg-white px-4 py-4 shadow-sm">
            <div class="min-w-0 space-y-1">
                <span class="block text-[10px] font-semibold uppercase tracking-wider text-neutral-400">
                    {{ t('vehicle.seller.phoneLabel') }}
                </span>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="block text-2xl font-semibold tracking-tight text-neutral-800">
                        {{ visiblePhone }}
                    </span>
                    <span
                        v-if="!isPhoneVisible"
                        class="rounded-full bg-neutral-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-neutral-500"
                    >
                        {{ t('vehicle.seller.phoneMaskedLabel') }}
                    </span>
                </div>
                <p class="text-xs text-neutral-500">
                    {{ t('vehicle.seller.phoneHint', { code: contactCode }) }}
                </p>
            </div>

            <div class="flex flex-col gap-2">
                <UiButton
                    v-if="store && store.whatsapp_number"
                    variant="secondary"
                    size="md"
                    type="button"
                    :loading="isTrackingPhoneView"
                    class="w-full sm:w-auto"
                    @click="handlePhoneAction"
                >
                    {{ isPhoneVisible ? t('vehicle.seller.phoneHideAction') : t('vehicle.seller.phoneAction') }}
                </UiButton>

                <UiButton
                    v-if="store"
                    variant="outline"
                    size="md"
                    type="button"
                    class="w-full sm:w-auto"
                    @click="handleStoreNavigate"
                >
                    {{ t('vehicle.seller.viewStore') }}
                </UiButton>
            </div>
        </div>

        <NuxtLink
            v-if="store"
            :to="`/lojas/${store.slug}`"
            class="inline-flex items-center gap-2 text-base font-semibold text-neutral-800 transition hover:text-brand-600"
        >
            <span>{{ t('vehicle.seller.viewAllVehicles') }}</span>
            <iconify-icon icon="tabler:chevron-right" class="text-lg"></iconify-icon>
        </NuxtLink>
    </section>
</template>
