import { readonly } from 'vue';

const ptBR = {
    appName: 'AutoHub',
    appSub: 'Backoffice',
    adminName: 'Tiago Silva',
    adminRole: 'Administrador Central',
    connectedDb: 'Conectado ao PostgreSQL',
    activeStores: 'Lojas Ativas',
    activeStoresSub: '+1 criada esta semana',
    totalVehicles: 'Veículos Anunciados',
    totalVehiclesSub: '+18% de aumento este mês',
    totalLeads: 'Propostas / Leads',
    totalLeadsSub: 'Conversão de leads de 24%',
    monthlyRevenue: 'Faturamento Mensal',
    monthlyRevenueSub: 'Recorrência Mensal Assinaturas',

    navigation: {
        dashboard: 'Painel Principal',
        stores: 'Gerenciar Lojas',
        wizard: 'Nova Loja (Tenant)',
        security: 'Segurança & E-mails',
        globalSupport: 'Suporte Global',
        myTickets: 'Meus Chamados',
    },

    headers: {
        dashboard: 'Painel de Métricas Central',
        stores: 'Lojas e Revendas Cadastradas',
        wizard: 'Adicionar Novo Tenant (Multi-Loja)',
        security: 'Simulador de Segurança & E-mails SMTP',
        globalSupport: 'Central de Suporte (Help Desk)',
        myTickets: 'Meus Chamados',
        subtitle: 'Estatísticas do ecossistema de marketplace em tempo real',
    },

    dashboard: {
        revenueChartTitle: 'Visão Geral do Faturamento de Assinaturas',
        tiers: {
            annual: 'Plano Anual (45% das revendas)',
            semiannual: 'Plano Semestral (35% das revendas)',
            monthly: 'Plano Mensal (20% das revendas)',
        },
        supportTitle: 'Suporte & Integração',
        supportDesc:
            'Cada nova loja cadastrada gera automaticamente um banco de dados isolado logicamente, sitemaps configurados no Redis e um site exclusivo no subdomínio correspondente.',
        supportBtn: 'Adicionar Nova Revenda',
    },

    stores: {
        title: 'Relação de Tenants de Revendas',
        btnNew: 'Novo Tenant',
        columns: {
            id: 'ID',
            store: 'Revenda',
            domain: 'Domínio / Host',
            plan: 'Plano Ativo',
            createdAt: 'Data Cadastro',
            status: 'Status',
            actions: 'Ações',
        },
        actions: {
            deactivate: 'Desativar',
            reactivate: 'Reativar',
        },
        plans: {
            mensal: 'Mensal - R$ 199/mês',
            semestral: 'Semestral - R$ 999/sem',
            anual: 'Anual - R$ 1.800/ano',
        },
    },

    wizard: {
        title: 'Assistente de Criação de Tenant',
        desc: 'Preencha os campos abaixo para provisionar um novo storefront e domínio para a revenda parceira.',
        successMsg: 'Tenant criado e ativado com sucesso no banco de dados!',
        fields: {
            name: 'Nome da Revenda',
            namePlaceholder: 'Ex: Euro Motors',
            subdomain: 'Subdomínio (Host)',
            subdomainPlaceholder: 'Ex: euro-motors',
            plan: 'Plano Assinatura',
            accentColor: 'Cor Temática',
            submitBtn: 'Provisionar Tenant Automático',
        },
        colors: {
            indigo: 'Indigo (Padrão)',
            amber: 'Laranja / Amber',
            red: 'Vermelho SP',
            blue: 'Azul Euro',
        },
    },

    security: {
        simulatorControl: 'Controle do Simulador',
        emailSelectLabel: 'Selecione o E-mail de Teste',
        btnSendReset: 'Enviar Link de Recuperação',
        btnSendVerify: 'Enviar Validação de E-mail',
        btnVerifyDirect: 'Validar E-mail Diretamente (Ignorar Link)',
        formNewPassword: 'Formulário de Nova Senha',
        tokenLabel: 'Código de Verificação (6 dígitos obtidos no Mailpit)',
        tokenPlaceholder: 'Ex: 129845',
        passwordLabel: 'Nova Senha',
        passwordConfirmLabel: 'Confirmar Nova Senha',
        passwordPlaceholder: '••••••••',
        btnResetSubmit: 'Redefinir Senha e Atualizar Hash',
        servicesStatus: 'Status dos Serviços Ativos',
        smtp: {
            title: 'Mail Service (SMTP Dev)',
            port: 'Porta 1025',
            desc: 'Servidor SMTP local para captura de e-mails transacionais. Visualize todos os envios de links de redefinição e chaves no painel Web Mailbox.',
            btn: 'Abrir SMTP Mailbox UI (:8025)',
        },
        s3: {
            title: 'S3 Object Storage',
            port: 'Porta 9001',
            desc: 'Armazenamento de objetos compatível com AWS S3. O console web para gerenciamento local está disponível diretamente na porta 9001.',
            btn: 'Abrir S3 Console Web (:9001)',
        },
    },
};

export const useI18n = () => {
    const t = (key: string, replacements?: Record<string, string | number>): string => {
        const keys = key.split('.');
        let value: any = ptBR;

        for (const k of keys) {
            if (value && typeof value === 'object' && k in value) {
                value = value[k];
            } else {
                return key;
            }
        }

        if (typeof value !== 'string') {
            return key;
        }

        if (replacements) {
            let result = value;
            for (const [placeholder, val] of Object.entries(replacements)) {
                result = result.replace(`{${placeholder}}`, String(val));
            }
            return result;
        }

        return value;
    };

    return {
        t,
        locale: 'pt-BR',
        currency: 'BRL',
    };
};
