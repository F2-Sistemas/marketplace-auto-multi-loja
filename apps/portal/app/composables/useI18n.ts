import { readonly } from 'vue';

const ptBR = {
    appName: 'AutoHub Central',
    appSub: 'Central',
    common: {
        cancel: 'Cancelar',
    },
    navigation: {
        services: 'Serviços',
        servicesAutomotive: 'Serviços automotivos',
        servicesFipe: 'Tabela FIPE',
        servicesEvaluation: 'Avaliação veicular',
        servicesAnnouncement: 'Quero anunciar',
    },
    auth: {
        identify: 'Identifique-se',
        loginPrompt: 'Faça login para uma experiência ainda melhor! :)',
        loginButton: 'Fazer login',
        createAccountPrompt: 'Não tem uma conta?',
        createAccount: 'Criar conta',
        clientArea: 'Sou Cliente',
        clientAreaDesc: 'Acessar meus favoritos',
        partnerArea: 'Sou Lojista / Parceiro',
        partnerAreaDesc: 'Acessar meu painel administrativo',
        joinNetwork: 'Quero fazer parte da rede',
    },
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
        priceTrust: 'Preço verificado',
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
        seller: {
            title: 'Sobre o vendedor',
            responseBadge: 'Vendedor responde rápido',
            responseTooltip: 'Lojistas com tempo médio de resposta até 24 horas.',
            storeFallback: 'Loja parceira',
            locationFallback: 'Localização não informada',
            openNow: 'Loja ativa agora',
            closedNow: 'Loja indisponível',
            menuTitle: 'Mais ações',
            loginAction: 'Fazer login',
            loginDescription: 'Acesse recursos da sua conta',
            viewStore: 'Ver loja',
            viewStoreDescription: 'Abrir a página da concessionária',
            sinceLabel: 'Desde',
            sinceValue: 'Desde {value}',
            sinceFallback: 'cadastro recente',
            listingsLabel: 'Anúncios',
            listingsValue: '{count} anúncios',
            responseLabel: 'Atendimento',
            responseValue: 'Resposta rápida',
            phoneLabel: 'Telefone',
            phoneFallback: 'Telefone não informado',
            phoneMaskedFallback: 'Telefone oculto',
            phoneMaskedLabel: 'Oculto',
            phoneAction: 'Ver telefone',
            phoneHideAction: 'Ocultar telefone',
            phoneHint: 'Na ligação informe o código: {code}',
            viewAllVehicles: 'Ver todos os carros deste vendedor',
        },
        report: {
            title: 'Denunciar anúncio',
            modalTitle: 'Conte por que este anúncio precisa de revisão',
            description:
                'Use esta opção para sinalizar informações incorretas, anúncio duplicado ou conteúdo suspeito.',
            menuDescription: 'Abrir formulário de denúncia',
            reasonLabel: 'Motivo',
            detailsLabel: 'Detalhes',
            detailsPlaceholder: 'Descreva o problema com mais contexto, se quiser.',
            submitButton: 'Enviar denúncia',
            success: 'Denúncia enviada com sucesso.',
            failure: 'Não foi possível enviar a denúncia agora.',
            reasons: {
                incorrectInformation: 'Informações incorretas',
                suspectedFraud: 'Suspeita de fraude',
                duplicateListing: 'Anúncio duplicado',
                offensiveContent: 'Conteúdo ofensivo',
                alreadySold: 'Já vendido',
                other: 'Outro motivo',
            },
        },
    },

    sharing: {
        title: 'Compartilhar e salvar',
        subtitle: 'Compartilhe o anúncio ou salve em listas privadas e públicas.',
        whatsapp: 'WhatsApp',
        x: 'X',
        instagram: 'Instagram',
        email: 'E-mail',
        copy: 'Copiar link',
        addToList: 'Adicionar à lista',
        formMessage: 'Formar mensagem',
        composerTitle: 'Enviar por WhatsApp',
        composerDescription: 'Ajuste a mensagem e abra a conversa no WhatsApp.',
        composerMessageLabel: 'Mensagem',
        composerHint: 'O botão abre o WhatsApp com a mensagem pronta.',
        whatsappDraft: 'Olá! Tenho interesse no veículo {title} que vi no portal.',
        createListTitle: 'Adicionar à lista de interesse',
        createListDescription: 'Crie uma nova lista ou adicione este veículo a uma lista existente.',
        createNewList: 'Criar nova lista',
        nameLabel: 'Nome',
        descriptionLabel: 'Descrição',
        publicListLabel: 'Lista pública com link compartilhável',
        createAndAdd: 'Criar e adicionar veículo',
        existingLists: 'Listas existentes',
        noLists: 'Nenhuma lista criada ainda.',
        addHere: 'Adicionar aqui',
        openLink: 'Abrir link',
        publicBadge: 'Pública',
        privateBadge: 'Privada',
        instagramCopied: 'Texto copiado para compartilhar no Instagram.',
        linkCopied: 'Link copiado.',
        listCreatedSuccess: 'Lista criada e veículo adicionado.',
        createListError: 'Não foi possível criar a lista agora.',
        addedToListSuccess: 'Veículo adicionado à lista.',
        addToListError: 'Não foi possível adicionar o veículo à lista.',
    },

    interestLists: {
        title: 'Minhas listas de interesse',
        description: 'Crie listas privadas ou públicas para compartilhar veículos com compradores.',
        formTitle: 'Criar ou editar lista',
        formDescription: 'Use esta área para criar novas listas com nome, descrição e visibilidade.',
        nameLabel: 'Nome',
        descriptionLabel: 'Descrição',
        publicToggleLabel: 'Lista pública com link compartilhável',
        createButton: 'Criar lista',
        saveButton: 'Salvar lista',
        editButton: 'Editar',
        clearButton: 'Limpar',
        count: '{count} listas',
        reload: 'Recarregar',
        empty: 'Nenhuma lista criada ainda.',
        noDescription: 'Sem descrição',
        publicLink: 'Abrir link público',
        share: 'Compartilhar',
        publicBadge: 'Pública',
        privateBadge: 'Privada',
        vehiclesCount: '{count} veículos',
        loading: 'Carregando lista...',
        privatePageTitle: 'lista inexistente ou privada',
        privatePageBody: 'Esta lista não está disponível publicamente no momento.',
        viewVehicles: 'Ver veículos',
        publicPageTitle: 'Lista pública',
        publicPageDescription: 'Lista pública de veículos compartilhada por um comprador.',
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
