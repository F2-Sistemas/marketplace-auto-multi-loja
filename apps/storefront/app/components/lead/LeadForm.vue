<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from '~/composables/useI18n';
import UiInput from '~/components/ui/UiInput.vue';
import UiButton from '~/components/ui/UiButton.vue';

interface Props {
  vehicleId: number;
  initialMessage?: string;
  storeWhatsapp: string;
}
const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'success'): void;
}>();

const { t } = useI18n();

const name = ref('');
const email = ref('');
const phone = ref('');
const message = ref(props.initialMessage || '');
const loading = ref(false);
const success = ref(false);

const submitProposal = async () => {
  loading.value = true;
  try {
    await $fetch('http://localhost:8000/api/leads', {
      method: 'POST',
      body: {
        name: name.value,
        email: email.value,
        phone: phone.value,
        message: message.value,
        vehicle_id: props.vehicleId,
      },
    });

    success.value = true;
    emit('success');

    // Simulate WhatsApp Redirect
    setTimeout(() => {
      const cleanPhone = props.storeWhatsapp.replace(/\D/g, '');
      const encodedMsg = encodeURIComponent(message.value);
      const whatsappUrl = `https://wa.me/${cleanPhone}?text=${encodedMsg}`;
      if (typeof window !== 'undefined') {
        window.open(whatsappUrl, '_blank');
      }
    }, 1200);

  } catch (error) {
    console.error('Erro ao enviar proposta:', error);
    alert('Ocorreu um erro ao enviar a proposta. Por favor, tente novamente.');
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <form @submit.prevent="submitProposal" class="space-y-4 text-left">
    <div v-if="success" class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-6 rounded-xl text-center text-sm animate-pulse">
      <iconify-icon icon="tabler:circle-check" class="text-3xl mb-2 inline-block text-emerald-400"></iconify-icon>
      <p class="font-bold text-base mb-1">Proposta Registrada!</p>
      <p class="text-slate-400 text-xs">{{ t('detail.success') }}</p>
    </div>

    <div v-else class="space-y-4">
      <div>
        <label for="lead-name" class="block text-xs font-semibold text-slate-400 mb-1.5">{{ t('detail.form.name') }}</label>
        <UiInput id="lead-name" v-model="name" placeholder="Ex: João Silva" required />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="lead-email" class="block text-xs font-semibold text-slate-400 mb-1.5">{{ t('detail.form.email') }}</label>
          <UiInput id="lead-email" type="email" v-model="email" placeholder="joao@example.com" required />
        </div>
        <div>
          <label for="lead-phone" class="block text-xs font-semibold text-slate-400 mb-1.5">{{ t('detail.form.phone') }}</label>
          <UiInput id="lead-phone" type="tel" v-model="phone" placeholder="(11) 99999-9999" required />
        </div>
      </div>

      <div>
        <label for="lead-message" class="block text-xs font-semibold text-slate-400 mb-1.5">{{ t('detail.form.message') }}</label>
        <UiInput id="lead-message" type="textarea" v-model="message" placeholder="Escreva sua proposta..." required />
      </div>

      <UiButton type="submit" :loading="loading" class="w-full text-center py-3 select-none flex items-center justify-center font-bold">
        <iconify-icon icon="tabler:brand-whatsapp" class="text-lg mr-2 block"></iconify-icon>
        {{ t('detail.form.submit') }}
      </UiButton>
    </div>
  </form>
</template>
