import { ref } from 'vue';

const locale = ref('pt-BR');

const translations: Record<string, Record<string, string>> = {
    'pt-BR': {
        'search.placeholder': 'O que você está procurando hoje?',
        'inventory.title': 'Nosso Estoque Exclusivo',
        'inventory.subtitle': 'Todos os veículos passam por laudo cautelar 100% aprovado',
        'inventory.found': 'Encontrados',
        'specs.mileage': 'km',
        'specs.transmission': 'Câmbio',
        'specs.fuel': 'Combustível',
        'specs.year': 'Ano',
        'button.contact': 'Falar com Consultor',
        'button.whatsapp': 'WhatsApp Loja',
        'button.back': 'Voltar para o estoque',
        'detail.title': 'Detalhes do Veículo',
        'detail.specs': 'Especificações Rápidas',
        'detail.contact': 'Envie sua Proposta para o Whatsapp',
        'detail.form.name': 'Seu Nome',
        'detail.form.email': 'Seu E-mail',
        'detail.form.phone': 'WhatsApp / Telefone',
        'detail.form.message': 'Mensagem',
        'detail.form.submit': 'Falar via WhatsApp',
        'detail.success': 'Redirecionando para o consultor no WhatsApp...',
        'empty.title': 'Nenhum automóvel encontrado',
        'empty.subtitle': 'Tente digitar outros termos de pesquisa.',
        'simulator.title': 'Simulador de Tenant (Domínio)',
        'simulator.subtitle':
            'Altere o tenant ativo para testar a personalização visual, cores e sitemap dinâmicos do storefront.',
        'footer.copyright': 'Todos os direitos reservados. Portal Parceiro AutoHub.',
        'header.tagline': 'Exclusividade & Confiabilidade',
        'nav.home': 'Início',
        'nav.inventory': 'Estoque',
        'nav.about': 'Sobre Nós',
        'nav.contact': 'Falar Conosco',
        'nav.dealer_area': 'Área Lojista',
        'nav.my_tickets': 'Meus Chamados',
        'nav.customize_theme': 'Personalizar Tema',
        'nav.my_profile': 'Meu Perfil',
        'nav.contact_btn': 'Contato',
        'news': 'Notícias',
        'favorites.nav': 'Favoritos',
        'footer.powered_by': 'Concessionária Integrada AutoHub',
        'simulator.select_label': 'Selecione a concessionária simulando o host de requisição:',
        'inventory.badge': 'Estoque Atualizado Hoje',
        'inventory.hero.title_part1': 'Encontre seu',
        'inventory.hero.title_highlight': 'Próximo Carro',
        'detail.price_label': 'Preço de Venda',
        'detail.form.subtitle': 'Envie seus dados para atendimento imediato',
        'specs.mileage.label': 'Quilometragem',
        'specs.color': 'Cor',
        'specs.highlights': 'Destaques do Veículo',
        'specs.direction': 'Direção Hidráulica',
        'specs.air': 'Ar Condicionado',
        'specs.windows': 'Vidros Elétricos',
        'specs.multimedia': 'Central Multimídia',
        'specs.abs': 'Freios ABS',
        'specs.airbags': 'Airbags Frontais',
        'detail.special_price': 'Preço Especial',
        'detail.proposal.subtitle': 'Envie sua proposta e receba atendimento imediato',
        'detail.not_found.title': 'Veículo Não Encontrado',
        'detail.not_found.subtitle': 'O veículo que você está procurando pode ter sido vendido ou removido.',
        'detail.form.error': 'Ocorreu um erro ao enviar a proposta. Por favor, tente novamente.',
        'detail.form.registered': 'Proposta Registrada!',

        // About Page Translations
        'about.title': 'Sobre a {storeName}',
        'about.subtitle': 'Conheça a história de credibilidade e alta performance que guia a nossa concessionária.',
        'about.story.title': 'Nossa História',
        'about.story.content':
            'Somos uma concessionária comprometida em entregar os veículos mais refinados e robustos do mercado. Nossa missão é proporcionar segurança e excelência a cada quilômetro rodado, garantindo laudos 100% aprovados, revisões minuciosas e atendimento premium.',
        'about.pillars.quality.title': 'Qualidade Assegurada',
        'about.features.laudo': 'Laudo Cautelar 100% Aprovado',
        'about.pillars.rates.title': 'Taxas Exclusivas',
        'about.features.financing': 'Melhores Taxas de Financiamento',
        'about.pillars.provenance.title': 'Total Procedência',
        'about.features.warranty': 'Garantia e Procedência',
        'about.pillars.exchange.title': 'Troca Facilitada',
        'about.features.exchange': 'Melhor Avaliação do Seu Usado',
        'about.button.inventory': 'Ver Estoque Completo',

        // Contact Page Translations
        'contact.title': 'Fale Conosco',
        'contact.subtitle':
            'Tem dúvidas ou quer agendar uma visita? Escolha o canal de sua preferência ou envie uma mensagem!',
        'contact.info.title': 'Informações de Contato',
        'contact.info.whatsapp': 'WhatsApp',
        'contact.info.address': 'Endereço',
        'contact.info.hours': 'Funcionamento',
        'contact.info.hours.week': 'Segunda a Sexta: 08h às 18h',
        'contact.info.hours.sat': 'Sábados: 09h às 13h',
        'contact.form.title': 'Envie uma Mensagem',
        'contact.form.name': 'Seu Nome',
        'contact.form.name.placeholder': 'Ex: Lucas Oliveira',
        'contact.form.email': 'E-mail para Contato',
        'contact.form.email.placeholder': 'lucas@example.com',
        'contact.form.message': 'Sua Mensagem',
        'contact.form.message.placeholder': 'Olá! Gostaria de tirar uma dúvida...',
        'contact.form.submit': 'Enviar Mensagem',
        'contact.form.success': 'Mensagem enviada com sucesso! Entraremos em contato em breve.',

        // Profile Page Translations
        'profile.title': 'Meu Perfil',
        'profile.subtitle': 'Gerencie as suas informações de acesso à loja {storeName}.',
        'profile.name': 'Nome Completo',
        'profile.name.placeholder': 'Seu nome',
        'profile.email': 'E-mail',
        'profile.email.placeholder': 'seu@email.com',
        'profile.email.hint': 'Este e-mail é utilizado para acessar a plataforma.',
        'profile.password.title': 'Alterar Senha',
        'profile.password.subtitle': 'Preencha apenas se quiser modificar sua senha atual.',
        'profile.password.new': 'Nova Senha',
        'profile.password.new.placeholder': 'Mínimo 6 caracteres',
        'profile.password.confirm': 'Confirmar Nova Senha',
        'profile.password.confirm.placeholder': 'Repita a senha',
        'profile.button.saving': 'Salvando...',
        'profile.button.save': 'Salvar Perfil',
        'profile.success': 'Perfil atualizado com sucesso!',
        'profile.error': 'Erro ao atualizar perfil.',

        // Theme Page Translations
        'theme.title': 'Aparência e Tema',
        'theme.subtitle': 'Personalize a identidade visual da sua vitrine de veículos.',
        'theme.success': 'Configurações de tema salvas com sucesso!',
        'theme.section.colors': 'Cores Principais',
        'theme.section.template': 'Template da Loja',
        'theme.template.label': 'Escolha o layout',
        'theme.template.helper':
            'A cor continua personalizável, mas o template muda a estrutura real da loja.',
        'theme.color.accent': 'Cor de Destaque (Ação)',
        'theme.color.placeholder': '#HEX',
        'theme.color.accent.hint': 'Usada em botões e destaques principais.',
        'theme.color.secondary': 'Cor Secundária',
        'theme.color.secondary.hint': 'Usada no cabeçalho e rodapé.',
        'theme.section.typo': 'Tipografia e Layout',
        'theme.font.label': 'Fonte Principal',
        'theme.layout.label': 'Template / Layout',
        'theme.button.saving': 'Salvando...',
        'theme.button.save': 'Salvar Tema',
        'theme.layout.showroom': 'Showroom Premium',
        'theme.layout.showroom.description':
            'Hero forte, cards amplos e apresentação mais editorial para estoque premium.',
        'theme.layout.showroom.benefit1': 'Mais foco em destaque visual',
        'theme.layout.showroom.benefit2': 'Ideal para poucas ofertas premium',
        'theme.layout.showroom.benefit3': 'Melhor para branding forte',
        'theme.layout.catalog': 'Catálogo Comercial',
        'theme.layout.catalog.description':
            'Layout mais objetivo, compacto e direto para mostrar mais veículos por tela.',
        'theme.layout.catalog.benefit1': 'Mais veículos visíveis por página',
        'theme.layout.catalog.benefit2': 'Leitura rápida e objetiva',
        'theme.layout.catalog.benefit3': 'Bom para estoque grande',
        'theme.layout.selected': 'Selecionado',
    },
};

export function useI18n() {
    const t = (key: string, replacements?: Record<string, string | number>): string => {
        const value = translations[locale.value]?.[key];
        if (!value) return key;
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
        locale,
    };
}
