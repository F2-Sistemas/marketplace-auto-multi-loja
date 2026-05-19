import { ref } from 'vue';

export const useSecurity = () => {
  const securityEmail = ref('admin@rederevenda.com');
  const securityToken = ref('');
  const securityPassword = ref('');
  const securityPasswordConfirm = ref('');
  const securityStatusMsg = ref('');
  const securityStatusType = ref<'success' | 'error' | ''>('');
  const loading = ref(false);

  const handleSendResetEmail = async () => {
    loading.value = true;
    try {
      securityStatusMsg.value = '';
      await $fetch('http://localhost:7031/api/auth/password/email', {
        method: 'POST',
        body: { email: securityEmail.value },
      });
      securityStatusType.value = 'success';
      securityStatusMsg.value =
        'Sucesso! E-mail de recuperação de senha enviado com sucesso via SMTP! Verifique o SMTP Sandbox em http://localhost:8025/';
    } catch (err: any) {
      securityStatusType.value = 'error';
      securityStatusMsg.value = err.data?.message || 'Erro ao disparar e-mail de recuperação.';
    } finally {
      loading.value = false;
    }
  };

  const handleResetPassword = async () => {
    loading.value = true;
    try {
      securityStatusMsg.value = '';
      await $fetch('http://localhost:7031/api/auth/password/reset', {
        method: 'POST',
        body: {
          email: securityEmail.value,
          token: securityToken.value,
          password: securityPassword.value,
          password_confirmation: securityPasswordConfirm.value,
        },
      });
      securityStatusType.value = 'success';
      securityStatusMsg.value = 'Sucesso! Senha redefinida e atualizada no banco de dados!';
      securityToken.value = '';
      securityPassword.value = '';
      securityPasswordConfirm.value = '';
    } catch (err: any) {
      securityStatusType.value = 'error';
      securityStatusMsg.value = err.data?.message || 'Erro ao redefinir a senha.';
    } finally {
      loading.value = false;
    }
  };

  const handleSendVerifyEmail = async () => {
    loading.value = true;
    try {
      securityStatusMsg.value = '';
      await $fetch('http://localhost:7031/api/auth/email/send-verification', {
        method: 'POST',
        body: { email: securityEmail.value },
      });
      securityStatusType.value = 'success';
      securityStatusMsg.value =
        'Sucesso! E-mail de validação de conta enviado com sucesso via SMTP! Verifique a Caixa de Entrada de Desenvolvimento.';
    } catch (err: any) {
      securityStatusType.value = 'error';
      securityStatusMsg.value = err.data?.message || 'Erro ao disparar e-mail de validação.';
    } finally {
      loading.value = false;
    }
  };

  const handleVerifyEmailDirect = async () => {
    loading.value = true;
    try {
      securityStatusMsg.value = '';
      await $fetch('http://localhost:7031/api/auth/email/verify', {
        method: 'POST',
        body: { email: securityEmail.value },
      });
      securityStatusType.value = 'success';
      securityStatusMsg.value = 'Sucesso! Conta de e-mail ativada e validada com sucesso no banco de dados!';
    } catch (err: any) {
      securityStatusType.value = 'error';
      securityStatusMsg.value = err.data?.message || 'Erro ao validar e-mail.';
    } finally {
      loading.value = false;
    }
  };

  return {
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
  };
};
