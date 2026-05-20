<script setup lang="ts">
import { onMounted } from 'vue';

onMounted(() => {
    const currentHost = window.location.hostname;

    if (currentHost === 'localhost' || currentHost === '127.0.0.1') {
        // Redirect to the local backoffice port (7032)
        window.location.href = `http://${currentHost}:7032`;
    } else {
        // Redirect to the backoffice subdomain in domain mode using HTTPS/active protocol
        const domainParts = currentHost.split('.');
        const protocol = window.location.protocol;
        if (domainParts.length >= 2) {
            const baseDomain = domainParts.slice(-2).join('.');
            window.location.href = `${protocol}//admin.${baseDomain}`;
        } else {
            window.location.href = `${protocol}//admin.rederevenda.com`;
        }
    }
});
</script>

<template>
    <div class="min-h-screen bg-neutral-50 flex flex-col items-center justify-center p-6 text-center font-sans">
        <div class="space-y-4 max-w-sm">
            <div
                class="w-12 h-12 rounded-lg bg-brand-500 flex items-center justify-center text-white mx-auto shadow-md animate-pulse"
            >
                <iconify-icon icon="tabler:arrow-up-right" class="text-2xl"></iconify-icon>
            </div>
            <h3 class="text-lg font-semibold text-neutral-800">Redirecionando...</h3>
            <p class="text-xs text-neutral-400 font-normal leading-relaxed">
                Você está sendo redirecionado para o Painel Administrativo (Backoffice).
            </p>
        </div>
    </div>
</template>
