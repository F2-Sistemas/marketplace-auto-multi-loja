<script setup lang="ts">
import { ref } from 'vue';
import UiCard from '~/components/ui/UiCard.vue';
import UiInput from '~/components/ui/UiInput.vue';
import UiButton from '~/components/ui/UiButton.vue';
import { useApi } from '~/composables/useApi';

const { getApiUrl } = useApi();

const profile = ref({
    name: 'Administrador TS',
    email: 'admin@autohub.com',
    password: '',
    password_confirmation: '',
});

const isSaving = ref(false);

const saveProfile = async () => {
    isSaving.value = true;
    try {
        const res = await $fetch(getApiUrl('/api/auth/profile'), {
            method: 'PUT',
            headers: {
                Accept: 'application/json',
                'X-Test-User-Email': 'admin@autohub.com', // Simulate auth
            },
            body: profile.value,
        });
        alert((res as any).message || 'Perfil atualizado com sucesso!');
        // Clear passwords
        profile.value.password = '';
        profile.value.password_confirmation = '';
    } catch (err: any) {
        const msg = err.data?.message || 'Erro ao atualizar perfil.';
        alert(msg);
    } finally {
        isSaving.value = false;
    }
};
</script>

<template>
    <div class="space-y-6 max-w-2xl mx-auto w-full">
        <div class="mb-4">
            <h2 class="text-xl font-semibold text-white">Meu Perfil</h2>
            <p class="text-slate-400 text-sm">Gerencie suas informações de usuário e senha de acesso.</p>
        </div>

        <UiCard class="p-6 border border-slate-800 bg-slate-900/50 space-y-6">
            <form @submit.prevent="saveProfile" class="space-y-6">
                <div class="space-y-4">
                    <!-- Nome -->
                    <div class="space-y-2">
                        <label class="block text-sm font-normal text-slate-300">Nome Completo</label>
                        <UiInput v-model="profile.name" type="text" placeholder="Seu nome" required />
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="block text-sm font-normal text-slate-300">E-mail</label>
                        <UiInput v-model="profile.email" type="email" placeholder="seu@email.com" required />
                    </div>

                    <!-- Divider -->
                    <div class="pt-4 border-t border-slate-800"></div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label class="block text-sm font-normal text-slate-300">Nova Senha (opcional)</label>
                        <UiInput v-model="profile.password" type="password" placeholder="Mínimo 6 caracteres" />
                        <p class="text-xs text-slate-500">Deixe em branco se não quiser alterar a senha.</p>
                    </div>

                    <!-- Password Confirmation -->
                    <div class="space-y-2" v-if="profile.password">
                        <label class="block text-sm font-normal text-slate-300">Confirmar Nova Senha</label>
                        <UiInput v-model="profile.password_confirmation" type="password" placeholder="Repita a senha" />
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <UiButton type="submit" variant="primary" :disabled="isSaving" class="w-full sm:w-auto">
                        <iconify-icon
                            v-if="isSaving"
                            icon="tabler:loader"
                            class="animate-spin text-lg mr-2"
                        ></iconify-icon>
                        {{ isSaving ? 'Salvando...' : 'Salvar Alterações' }}
                    </UiButton>
                </div>
            </form>
        </UiCard>
    </div>
</template>
