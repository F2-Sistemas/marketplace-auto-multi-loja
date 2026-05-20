<script setup lang="ts">
import { useI18n } from '../../composables/useI18n';
import { useSecurity } from '../../composables/useSecurity';

const { t } = useI18n();
const {
    securityEmail,
    securityToken,
    securityPassword,
    securityPasswordConfirm,
    securityStatusMsg,
    securityStatusType,
    loading,
    handleSendResetEmail,
    handleResetPassword,
    handleSendVerifyEmail,
    handleVerifyEmailDirect,
} = useSecurity();

const emails = [
    { value: 'admin@rederevenda.com', label: 'admin@rederevenda.com (Administrador)' },
    { value: 'tiago@rederevenda.com', label: 'tiago@rederevenda.com (Parceiro)' },
    { value: 'usuario@exemplo.com', label: 'usuario@exemplo.com (Cliente Final)' },
];
</script>

<template>
    <div class="space-y-8 animate-fadeIn">
        <!-- API RESPONSES ALERT BANNER -->
        <div
            v-if="securityStatusMsg"
            :class="[
                'p-4 rounded-xl border flex gap-3 items-start transition-all',
                securityStatusType === 'success'
                    ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400'
                    : 'bg-rose-500/10 border-rose-500/20 text-rose-400',
            ]"
        >
            <iconify-icon
                :icon="securityStatusType === 'success' ? 'tabler:circle-check' : 'tabler:alert-triangle'"
                class="text-xl shrink-0 mt-0.5"
            ></iconify-icon>
            <div class="text-xs font-semibold leading-normal">
                {{ securityStatusMsg }}
            </div>
        </div>

        <!-- MAIN SIMULATOR WIDGETS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- EMAIL DISPATCH CONTROLS -->
            <UiCard>
                <div class="border-b border-slate-800 pb-4 mb-6">
                    <h3 class="text-sm font-extrabold text-white">
                        {{ t('security.simulatorControl') }}
                    </h3>
                </div>

                <div class="space-y-6">
                    <UiSelect v-model="securityEmail" :label="t('security.emailSelectLabel')" :options="emails" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <UiButton type="button" variant="secondary" :disabled="loading" @click="handleSendResetEmail">
                            <iconify-icon icon="tabler:mail-forward" class="text-sm"></iconify-icon>
                            <span>{{ t('security.btnSendReset') }}</span>
                        </UiButton>

                        <UiButton type="button" variant="secondary" :disabled="loading" @click="handleSendVerifyEmail">
                            <iconify-icon icon="tabler:mail-opened" class="text-sm"></iconify-icon>
                            <span>{{ t('security.btnSendVerify') }}</span>
                        </UiButton>
                    </div>

                    <div class="pt-4 border-t border-slate-800">
                        <UiButton
                            type="button"
                            variant="primary"
                            class="w-full"
                            :disabled="loading"
                            @click="handleVerifyEmailDirect"
                        >
                            <iconify-icon icon="tabler:shield-check" class="text-sm"></iconify-icon>
                            <span>{{ t('security.btnVerifyDirect') }}</span>
                        </UiButton>
                    </div>
                </div>
            </UiCard>

            <!-- PASSWORD RECOVERY FORM -->
            <UiCard>
                <div class="border-b border-slate-800 pb-4 mb-6">
                    <h3 class="text-sm font-extrabold text-white">
                        {{ t('security.formNewPassword') }}
                    </h3>
                </div>

                <form @submit.prevent="handleResetPassword" class="space-y-4">
                    <UiInput
                        v-model="securityToken"
                        :label="t('security.tokenLabel')"
                        :placeholder="t('security.tokenPlaceholder')"
                        required
                    />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <UiInput
                            v-model="securityPassword"
                            type="password"
                            :label="t('security.passwordLabel')"
                            :placeholder="t('security.passwordPlaceholder')"
                            required
                        />

                        <UiInput
                            v-model="securityPasswordConfirm"
                            type="password"
                            :label="t('security.passwordConfirmLabel')"
                            :placeholder="t('security.passwordPlaceholder')"
                            required
                        />
                    </div>

                    <div class="pt-4 border-t border-slate-800 flex justify-end">
                        <UiButton type="submit" variant="primary" :disabled="loading">
                            <iconify-icon
                                :icon="loading ? 'tabler:loader' : 'tabler:lock-open'"
                                :class="['text-sm', loading ? 'animate-spin' : '']"
                            ></iconify-icon>
                            <span>{{ t('security.btnResetSubmit') }}</span>
                        </UiButton>
                    </div>
                </form>
            </UiCard>
        </div>

        <!-- ACTIVE PORTS OVERVIEW -->
        <div class="space-y-4">
            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-widest">
                {{ t('security.servicesStatus') }}
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SMTP CARD -->
                <UiCard class="hover:border-slate-700 transition-colors flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h5 class="text-sm font-extrabold text-white">
                                    {{ t('security.smtp.title') }}
                                </h5>
                                <span class="text-[9px] uppercase font-semibold text-indigo-400 mt-1 block">
                                    {{ t('security.smtp.port') }}
                                </span>
                            </div>
                            <UiBadge variant="success" pulse>Ativo</UiBadge>
                        </div>

                        <p class="text-xs text-slate-400 leading-relaxed mb-6">
                            {{ t('security.smtp.desc') }}
                        </p>
                    </div>

                    <UiButton
                        href="http://localhost:8025"
                        variant="secondary"
                        class="w-full text-center flex justify-center"
                    >
                        <iconify-icon icon="tabler:external-link" class="text-sm"></iconify-icon>
                        <span>{{ t('security.smtp.btn') }}</span>
                    </UiButton>
                </UiCard>

                <!-- S3 STORAGE CARD -->
                <UiCard class="hover:border-slate-700 transition-colors flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h5 class="text-sm font-extrabold text-white">
                                    {{ t('security.s3.title') }}
                                </h5>
                                <span class="text-[9px] uppercase font-semibold text-indigo-400 mt-1 block">
                                    {{ t('security.s3.port') }}
                                </span>
                            </div>
                            <UiBadge variant="success" pulse>Ativo</UiBadge>
                        </div>

                        <p class="text-xs text-slate-400 leading-relaxed mb-6">
                            {{ t('security.s3.desc') }}
                        </p>
                    </div>

                    <UiButton
                        href="http://localhost:9001"
                        variant="secondary"
                        class="w-full text-center flex justify-center"
                    >
                        <iconify-icon icon="tabler:external-link" class="text-sm"></iconify-icon>
                        <span>{{ t('security.s3.btn') }}</span>
                    </UiButton>
                </UiCard>
            </div>
        </div>
    </div>
</template>
