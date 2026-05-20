import { readonly } from 'vue';

const ptBR = {
    appName: 'AutoHub Central',
    appSub: 'Central',
    home: 'Início',
    catalog: 'Catálogo',
    stores: 'Lojas Parceiras',
    storesTitle: 'Nossas Lojas Parceiras',
    storesDesc: 'Encontre o veículo perfeito direto com as melhores revendas qualificadas do Brasil.',
    viewStores: 'Ver Lojas',
    adminPanel: 'Painel Admin',
    heroPre: 'Plataforma Multi-Loja Líder',
    heroTitle: 'A maior rede de revendas',
    heroTitleGradient: 'premium do Brasil',
    heroDesc:
        'Explore o estoque integrado de dezenas de lojas qualificadas, encontre o veículo perfeito com segurança e fale direto com o consultor.',
    hero: {
        title: 'A maior rede de revendas premium do Brasil',
        subtitle:
            'Explore o estoque integrado de dezenas de lojas qualificadas, encontre o veículo perfeito com segurança e fale direto com o consultor.',
    },

    metrics: {
        activeVehicles: '2.500+ Veículos',
        activeVehiclesSub: 'Veículos Ativos',
        partnerStores: '85+ Lojas',
        partnerStoresSub: 'Lojas Parceiras',
        states: '26 Estados',
        statesSub: 'Estados Atendidos',
        satisfaction: '99.8%',
        satisfactionSub: 'Satisfação Garantida',
    },

    search: {
        title: 'Filtre e Encontre Seu Carro',
        catalogTitle: 'Catálogo de Veículos',
        catalogSubtitle:
            'Encontre o veículo perfeito entre as centenas de ofertas exclusivas das melhores concessionárias da região.',
        placeholder: 'Digite marca, modelo...',
        allBrands: 'Todas as Marcas',
        allStores: 'Todas as Lojas',
        transmissionLabel: 'Câmbio (Todos)',
        transmissionAuto: 'Automático',
        transmissionManual: 'Manual',
        priceMaxLabel: 'Até:',
        clearFilters: 'Limpar Filtros',
        resultCount: 'Mostrando {count} veículos disponíveis',
        emptyStateTitle: 'Nenhum veículo encontrado',
        emptyStateDesc: 'Tente ajustar seus termos de busca ou filtros para ver outras opções de carros incríveis.',
        sortBy: 'Ordenar por',
        sortLabel: 'Ordenar por:',
        sortNewest: 'Mais Novos',
        sortPriceAsc: 'Menor Preço',
        sortPriceDesc: 'Maior Preço',
        sortOptions: {
            relevance: 'Relevância',
            priceAsc: 'Menor Preço',
            priceDesc: 'Maior Preço',
            yearDesc: 'Mais Novos',
        },
    },

    vehicle: {
        priceLabel: 'Preço',
        priceOnRequest: 'Sob consulta',
        featured: 'Destaque',
        viewDetails: 'Ver Detalhes',
        specsTitle: 'Ficha Técnica',
        sendProposal: 'Fale direto com a concessionária',
        sendButton: 'Enviar Proposta via AutoMarket',
        leadSuccessTitle: 'Proposta Enviada com Sucesso!',
        leadSuccessDescription: 'A concessionária entrará em contato em breve para dar andamento ao seu atendimento.',
        specs: {
            mileage: 'km',
            transmission: 'Câmbio',
            fuel: 'Combustível',
            color: 'Cor',
            year: 'Ano',
        },
        actions: {
            viewDetails: 'Ver Detalhes',
            backToList: 'Voltar para o catálogo',
        },
        details: {
            title: 'Detalhes do Veículo',
            specsTitle: 'Especificações Técnicas',
            priceSpecial: 'Valor Especial',
            storeName: 'Loja',
            location: 'Localização',
            sameStoreTitle: 'Outros veículos desta loja',
            similarTitle: 'Veículos similares que você pode gostar',
        },
    },

    lead: {
        formTitle: 'Fale direto com a concessionária',
        nameLabel: 'Seu Nome',
        namePlaceholder: 'Ex: Carlos Silva',
        emailLabel: 'Seu E-mail',
        emailPlaceholder: 'Ex: carlos@example.com',
        phoneLabel: 'WhatsApp / Telefone',
        phonePlaceholder: 'Ex: (84) 99999-9999',
        messageLabel: 'Mensagem de Interesse',
        submitButton: 'Enviar Proposta via AutoHub',
        successMsg: 'Proposta enviada com sucesso! A loja entrará em contato em breve.',
        errorMsg: 'Ocorreu um erro ao enviar a proposta. Por favor, tente novamente.',
        viewOnStore: 'Ver no site da loja',
    },

    favorites: {
        title: 'Meus Veículos Favoritos',
        titleAccount: 'Meus Favoritos',
        description:
            'Gerencie os veículos que você salvou para ver depois. Suas preferências são salvas automaticamente neste navegador.',
        emptyTitle: 'Sua lista está vazia',
        emptyDesc:
            'Navegue pelo nosso catálogo de veículos de concessionárias parceiras e favorite os modelos que mais lhe interessam!',
        searchVehicles: 'Buscar Veículos',
        countSingle: 'veículo salvo',
        countPlural: 'veículos salvos',
        refresh: 'Atualizar',
        breadcrumbAccount: 'Minha Conta',
    },

    footer: {
        copyright: '© 2026 AutoHub S.A. Todos os direitos reservados. Feito no Brasil.',
        terms: 'Termos de Uso',
        privacy: 'Política de Privacidade',
        admin: 'Administração',
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
