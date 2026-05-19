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
    'simulator.subtitle': 'Altere o tenant ativo para testar a personalização visual, cores e sitemap dinâmicos do storefront.',
    'footer.copyright': 'Todos os direitos reservados. Portal Parceiro AutoHub.',
    'header.tagline': 'Exclusividade & Confiabilidade',
    'nav.home': 'Início',
    'nav.inventory': 'Estoque',
    'nav.about': 'Sobre Nós',
    'nav.contact': 'Falar Conosco',
    'about.story.title': 'Nossa História',
    'about.story.content': 'Somos uma concessionária comprometida em entregar os veículos mais refinados e robustos do mercado. Nossa missão é proporcionar segurança e excelência a cada quilômetro rodado, garantindo laudos 100% aprovados, revisões minuciosas e atendimento premium.',
    'about.features.laudo': 'Laudo Cautelar 100% Aprovado',
    'about.features.financing': 'Melhores Taxas de Financiamento',
    'about.features.warranty': 'Garantia e Procedência',
    'about.features.exchange': 'Melhor Avaliação do Seu Usado',
    'contact.info.title': 'Informações de Contato',
    'contact.form.title': 'Envie uma Mensagem Direta',
    'contact.form.name': 'Nome Completo',
    'contact.form.email': 'Endereço de E-mail',
    'contact.form.phone': 'Telefone Celular',
    'contact.form.message': 'Escreva sua mensagem...',
    'contact.form.submit': 'Enviar Mensagem',
    'contact.success': 'Mensagem enviada com sucesso! Logo entraremos em contato.'
  }
};

export function useI18n() {
  const t = (key: string): string => {
    return translations[locale.value]?.[key] || key;
  };
  return {
    t,
    locale
  };
}
