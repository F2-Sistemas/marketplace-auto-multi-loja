<script setup lang="ts">
import { ref } from 'vue';
import UiCard from '~/components/ui/UiCard.vue';
import UiInput from '~/components/ui/UiInput.vue';
import UiButton from '~/components/ui/UiButton.vue';
import { useTenant } from '~/composables/useTenant';
import { useApi } from '~/composables/useApi';
import { useI18n } from '~/composables/useI18n';

const { currentStore } = useTenant();
const { getApiUrl } = useApi();
const { t } = useI18n();

const profile = ref({
    name: 'Lojista Padrão',
    email: 'lojista@autohub.com',
    password: '',
    password_confirmation: '',
});

const isSaving = ref(false);

const saveProfile = async () => {
    isSaving.value = true;
    try {
        const res = await $fetch<any>(getApiUrl('/api/auth/profile'), {
            method: 'PUT',
            headers: {
                Accept: 'application/json',
                'X-Test-User-Email': 'lojista@autohub.com', // Simulate auth
            },
            body: profile.value,
        });
        alert(res.message || t('profile.success'));
        // Clear passwords
        profile.value.password = '';
        profile.value.password_confirmation = '';
    } catch (err: any) {
        const msg = err.data?.message || t('profile.error');
        alert(msg);
    } finally {
        isSaving.value = false;
    }
};
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-100 tracking-tight">{{ t('profile.title') }}</h1>
            <p class="text-sm text-slate-400 mt-2">
                {{ t('profile.subtitle', { storeName: currentStore?.name || '' }) }}
            </p>
        </header>

        <UiCard class="max-w-2xl p-6 md:p-8 border border-slate-800 bg-slate-900/40 backdrop-blur-sm">
            <form @submit.prevent="saveProfile" class="space-y-6">
                <div class="space-y-5">
                    <!-- Nome -->
                    <div class="space-y-2">
                        <label class="block text-sm font-normal text-slate-300">{{ t('profile.name') }}</label>
                        <UiInput
                            v-model="profile.name"
                            type="text"
                            :placeholder="t('profile.name.placeholder')"
                            required
                        />
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="block text-sm font-normal text-slate-300">{{ t('profile.email') }}</label>
                        <UiInput
                            v-model="profile.email"
                            type="email"
                            :placeholder="t('profile.email.placeholder')"
                            required
                        />
                        <p class="text-xs text-slate-500">{{ t('profile.email.hint') }}</p>
                    </div>

                    <!-- Divider -->
                    <div class="pt-4 pb-2">
                        <div class="border-t border-slate-800"></div>
                    </div>

                    <div class="mb-2">
                        <h3 class="text-sm font-semibold text-slate-200">{{ t('profile.password.title') }}</h3>
                        <p class="text-xs text-slate-500">{{ t('profile.password.subtitle') }}</p>
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label class="block text-sm font-normal text-slate-300">
                            {{ t('profile.password.new') }}
                        </label>
                        <UiInput
                            v-model="profile.password"
                            type="password"
                            :placeholder="t('profile.password.new.placeholder')"
                        />
                    </div>

                    <!-- Password Confirmation -->
                    <div class="space-y-2" v-if="profile.password">
                        <label class="block text-sm font-normal text-slate-300">
                            {{ t('profile.password.confirm') }}
                        </label>
                        <UiInput
                            v-model="profile.password_confirmation"
                            type="password"
                            :placeholder="t('profile.password.confirm.placeholder')"
                        />
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <UiButton
                        type="submit"
                        variant="primary"
                        :disabled="isSaving"
                        class="w-full sm:w-auto store-primary-btn border-none"
                    >
                        <iconify-icon
                            v-if="isSaving"
                            icon="tabler:loader"
                            class="animate-spin text-lg mr-2"
                        ></iconify-icon>
                        {{ isSaving ? t('profile.button.saving') : t('profile.button.save') }}
                    </UiButton>
                </div>
            </form>
        </UiCard>
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
