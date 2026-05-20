<script setup lang="ts">
import { ref } from 'vue';
import { useVehicles } from '~/composables/useVehicles';
import { useI18n } from '~/composables/useI18n';
import UiInput from '~/components/ui/UiInput.vue';
import UiButton from '~/components/ui/UiButton.vue';

interface Props {
    vehicleId: number;
    vehicleTitle: string;
    storeName: string;
    storePhone?: string;
}

const props = defineProps<Props>();

const { postLead } = useVehicles();
const { t } = useI18n();

const name = ref('');
const email = ref('');
const phone = ref('');
const message = ref(`Olá, tenho interesse neste veículo (${props.vehicleTitle}). Aguardo o contato.`);

const errors = ref<{ name?: string; email?: string; phone?: string; message?: string }>({});
const loading = ref(false);
const success = ref(false);

const validate = () => {
    const currentErrors: typeof errors.value = {};

    if (!name.value.trim()) {
        currentErrors.name = 'O nome completo é obrigatório.';
    }
    if (!email.value.trim()) {
        currentErrors.email = 'O e-mail é obrigatório.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        currentErrors.email = 'Insira um e-mail válido.';
    }
    if (!phone.value.trim()) {
        currentErrors.phone = 'O telefone de contato é obrigatório.';
    }

    errors.value = currentErrors;
    return Object.keys(currentErrors).length === 0;
};

const handleSubmit = async () => {
    if (!validate()) return;

    loading.value = true;
    try {
        const payload = {
            vehicle_id: props.vehicleId,
            name: name.value,
            email: email.value,
            phone: phone.value,
            message: message.value,
        };

        await postLead(payload);
        success.value = true;
    } catch (err) {
        alert('Erro ao enviar a proposta. Por favor, verifique seus dados ou tente novamente.');
    } finally {
        loading.value = false;
    }
};

const handleWhatsApp = () => {
    if (!props.storePhone) return;
    const cleanPhone = props.storePhone.replace(/\D/g, '');
    const text = encodeURIComponent(`Olá! Tenho interesse no veículo ${props.vehicleTitle} que vi no portal.`);
    window.open(`https://wa.me/55${cleanPhone}?text=${text}`, '_blank');
};
</script>

<template>
    <div class="border border-neutral-200 bg-white rounded-card overflow-hidden shadow-sm p-6 space-y-4">
        <!-- Success visual block -->
        <div v-if="success" class="text-center py-6 space-y-4 animate-in fade-in zoom-in-95">
            <div
                class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto border border-emerald-100"
            >
                <iconify-icon icon="tabler:circle-check" class="text-2xl"></iconify-icon>
            </div>

            <div class="space-y-1">
                <h3 class="font-extrabold text-neutral-800 text-base">
                    {{ t('vehicle.leadSuccessTitle') }}
                </h3>
                <p class="text-xs text-neutral-500 font-normal max-w-xs mx-auto">
                    {{ t('vehicle.leadSuccessDescription') }}
                </p>
            </div>

            <!-- Quick WhatsApp triggers -->
            <div v-if="storePhone" class="pt-4 space-y-2">
                <p class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider">
                    Ou converse agora mesmo
                </p>
                <UiButton
                    variant="primary"
                    size="md"
                    class="w-full !bg-emerald-600 hover:!bg-emerald-700 !border-emerald-600 focus:ring-emerald-100 shadow-sm shadow-emerald-600/10 font-semibold"
                    @click="handleWhatsApp"
                >
                    <template #icon>
                        <iconify-icon icon="tabler:brand-whatsapp" class="text-lg"></iconify-icon>
                    </template>
                    <span>Falar no WhatsApp</span>
                </UiButton>
            </div>
        </div>

        <!-- Live Form sheet -->
        <form v-else @submit.prevent="handleSubmit" class="space-y-4">
            <div>
                <h3 class="font-extrabold text-neutral-800 text-base">
                    {{ t('vehicle.sendProposal') }}
                </h3>
                <p class="text-xs text-neutral-400 font-medium">
                    Preencha o formulário e a concessionária {{ storeName }} entrará em contato.
                </p>
            </div>

            <div class="space-y-3.5">
                <!-- Name -->
                <div class="space-y-1">
                    <label for="lead-name" class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider">
                        Nome Completo
                    </label>
                    <UiInput
                        v-model="name"
                        placeholder="Digite seu nome completo"
                        :error="errors.name"
                        :disabled="loading"
                        id="lead-name"
                    />
                </div>

                <!-- Email -->
                <div class="space-y-1">
                    <label for="lead-email" class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider">
                        E-mail
                    </label>
                    <UiInput
                        v-model="email"
                        type="email"
                        placeholder="seuemail@exemplo.com.br"
                        :error="errors.email"
                        :disabled="loading"
                        id="lead-email"
                    />
                </div>

                <!-- Phone -->
                <div class="space-y-1">
                    <label for="lead-phone" class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider">
                        Telefone / Celular
                    </label>
                    <UiInput
                        v-model="phone"
                        placeholder="(11) 99999-9999"
                        :error="errors.phone"
                        :disabled="loading"
                        id="lead-phone"
                    />
                </div>

                <!-- Message -->
                <div class="space-y-1">
                    <label
                        for="lead-message"
                        class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider"
                    >
                        Mensagem
                    </label>
                    <textarea
                        v-model="message"
                        rows="3"
                        id="lead-message"
                        :disabled="loading"
                        placeholder="Digite sua proposta ou dúvida..."
                        class="w-full p-3.5 bg-white border border-neutral-200 text-sm text-neutral-800 placeholder-neutral-400 outline-none rounded-input transition-all duration-150 focus:border-neutral-300 focus:ring-1 focus:ring-neutral-200"
                    ></textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="space-y-2 pt-2">
                <UiButton
                    type="submit"
                    variant="primary"
                    size="md"
                    class="w-full shadow-sm shadow-brand-500/10 font-semibold"
                    :loading="loading"
                >
                    <span>{{ t('vehicle.sendButton') }}</span>
                </UiButton>

                <!-- Dynamic Direct WhatsApp Trigger -->
                <UiButton
                    v-if="storePhone"
                    type="button"
                    variant="outline"
                    size="md"
                    class="w-full hover:bg-emerald-50/50 hover:border-emerald-200 hover:text-emerald-700 font-semibold"
                    @click="handleWhatsApp"
                >
                    <template #icon>
                        <iconify-icon
                            icon="tabler:brand-whatsapp"
                            class="text-base text-emerald-500 group-hover:text-emerald-700"
                        ></iconify-icon>
                    </template>
                    <span>Conversar por WhatsApp</span>
                </UiButton>
            </div>
        </form>
    </div>
</template>
