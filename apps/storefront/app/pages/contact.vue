<script setup lang="ts">
import { ref } from 'vue';
import { useTenant } from '~/composables/useTenant';
import { useI18n } from '~/composables/useI18n';
import UiCard from '~/components/ui/UiCard.vue';
import UiInput from '~/components/ui/UiInput.vue';
import UiButton from '~/components/ui/UiButton.vue';

const { currentStore } = useTenant();
const { t } = useI18n();

const name = ref('');
const email = ref('');
const msg = ref('');
const loading = ref(false);
const sent = ref(false);

const sendMessage = async () => {
    loading.value = true;
    await new Promise((resolve) => setTimeout(resolve, 800));
    sent.value = true;
    loading.value = false;
    name.value = '';
    email.value = '';
    msg.value = '';
};
</script>

<template>
    <div class="py-12 md:py-20 bg-slate-950">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Title -->
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-black text-slate-100 mb-4 tracking-tight">
                    {{ t('contact.title') }}
                </h2>
                <p class="text-slate-500 text-sm max-w-lg mx-auto font-medium leading-relaxed">
                    {{ t('contact.subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Contact details -->
                <div class="lg:col-span-1 space-y-6 text-left">
                    <UiCard class="p-6 border-slate-800 bg-slate-900/40">
                        <h3 class="font-semibold text-slate-200 text-base mb-4">{{ t('contact.info.title') }}</h3>
                        <div class="space-y-4">
                            <!-- Whatsapp -->
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0 mt-0.5"
                                >
                                    <iconify-icon icon="tabler:brand-whatsapp" class="text-lg block"></iconify-icon>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-500 block font-normal uppercase">
                                        {{ t('contact.info.whatsapp') }}
                                    </span>
                                    <a
                                        :href="`https://wa.me/${currentStore.whatsapp}`"
                                        target="_blank"
                                        class="text-xs font-semibold text-slate-200 hover:text-emerald-400 transition-colors"
                                    >
                                        {{ currentStore.phone }}
                                    </a>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0 mt-0.5"
                                >
                                    <iconify-icon icon="tabler:map-pin" class="text-lg block"></iconify-icon>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-500 block font-normal uppercase">
                                        {{ t('contact.info.address') }}
                                    </span>
                                    <span class="text-xs text-slate-350 leading-relaxed font-medium">
                                        {{ currentStore.address }} — {{ currentStore.city }}/{{ currentStore.state }}
                                    </span>
                                </div>
                            </div>

                            <!-- Opening Hours -->
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0 mt-0.5"
                                >
                                    <iconify-icon icon="tabler:clock" class="text-lg block"></iconify-icon>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-500 block font-normal uppercase">
                                        {{ t('contact.info.hours') }}
                                    </span>
                                    <span class="text-xs text-slate-350 leading-relaxed font-medium">
                                        {{ t('contact.info.hours.week') }}
                                        <br />
                                        {{ t('contact.info.hours.sat') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </UiCard>
                </div>

                <!-- Form Box -->
                <div class="lg:col-span-2 text-left">
                    <UiCard class="p-6 md:p-8 border-slate-800 bg-slate-900/40">
                        <h3 class="font-semibold text-slate-200 text-base mb-6">{{ t('contact.form.title') }}</h3>

                        <form @submit.prevent="sendMessage" class="space-y-4">
                            <div
                                v-if="sent"
                                class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-450 p-4 rounded-xl text-center text-xs font-normal"
                            >
                                {{ t('contact.form.success') }}
                            </div>

                            <div v-else class="space-y-4">
                                <div>
                                    <label for="contact-name" class="block text-xs font-normal text-slate-400 mb-1.5">
                                        {{ t('contact.form.name') }}
                                    </label>
                                    <UiInput
                                        id="contact-name"
                                        v-model="name"
                                        :placeholder="t('contact.form.name.placeholder')"
                                        required
                                    />
                                </div>

                                <div>
                                    <label for="contact-email" class="block text-xs font-normal text-slate-400 mb-1.5">
                                        {{ t('contact.form.email') }}
                                    </label>
                                    <UiInput
                                        id="contact-email"
                                        type="email"
                                        v-model="email"
                                        :placeholder="t('contact.form.email.placeholder')"
                                        required
                                    />
                                </div>

                                <div>
                                    <label for="contact-msg" class="block text-xs font-normal text-slate-400 mb-1.5">
                                        {{ t('contact.form.message') }}
                                    </label>
                                    <UiInput
                                        id="contact-msg"
                                        type="textarea"
                                        v-model="msg"
                                        :placeholder="t('contact.form.message.placeholder')"
                                        required
                                    />
                                </div>

                                <UiButton
                                    type="submit"
                                    :loading="loading"
                                    class="w-full text-center py-3 select-none flex items-center justify-center font-semibold"
                                >
                                    {{ t('contact.form.submit') }}
                                </UiButton>
                            </div>
                        </form>
                    </UiCard>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.store-text-color {
    color: var(--store-accent-color, #fbbf24);
}
</style>
