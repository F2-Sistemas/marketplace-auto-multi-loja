# Visão Geral do Projeto - AutoHub Central

O **AutoHub Central** é um marketplace e hub completo de veículos automotores estruturado sob uma arquitetura de monorepo moderno. Ele foi desenhado para consolidar anúncios de veículos de múltiplas lojas/revendas em um portal central e público, ao mesmo tempo que gera um site/storefront único e personalizado para cada loja individual cadastrada na plataforma.

---

## Estrutura do Monorepo

O código-fonte do ecossistema está contido nos seguintes diretórios principais:

```text
/apps
  /api          -> Laravel API Central (API-only)
  /portal       -> Nuxt 4 do Marketplace Público
  /storefront   -> Nuxt 4 de Sites de Lojas (Aplicação Única Dinâmica)
  /backoffice   -> Nuxt 4 Administrativo (Admin Central e Lojista)
/packages
  /ui           -> Design Tokens e Componentes Compartilhados (Opcional)
  /types        -> Contratos de Tipos e Interfaces TypeScript (Opcional)
```

---

## Responsabilidade de Cada Aplicação (Apps)

### 1. `/apps/api` (Laravel API Central)
A API central atua como a única fonte de verdade de regras de negócio, persistência e integrações do sistema. É uma aplicação Laravel puramente **API-only**.
* **Principais Funções**:
  - Autenticação e controle de acesso (Roles e Permissions via Policies).
  - Gestão de Lojas (Tenants) e ciclo de vida de domínios.
  - CRUD e moderação de Veículos, Opcionais, Ocorrências e Imagens.
  - Interpretação semântica de payloads de busca e rotas de SEO.
  - Orquestração de filas (Redis) e jobs de geração assíncrona de sitemaps.
  - Gestão de planos, assinaturas e logs completos de auditoria.

### 2. `/apps/portal` (Marketplace Central Nuxt)
É o portal principal público que os visitantes utilizam para buscar automóveis em todas as lojas vinculadas.
* **Principais Funções**:
  - Página Inicial do Marketplace com busca global e destaque de anúncios/lojas.
  - Listagem de veículos com filtros avançados (marca, preço, quilometragem, combustível, localização, etc.).
  - Visualização detalhada do veículo com informações técnicas, opcionais e fotos.
  - Página institucional da loja dentro do portal (estoque próprio, dados de contato e link para o site próprio).
  - Favoritos (para usuários logados) e formulários de conversas/leads.
  - CTA explícito "Ver no site da loja".

### 3. `/apps/storefront` (Sites das Lojas Nuxt)
Uma **única aplicação** Nuxt 4 configurada para atender a **N lojas**. O storefront reconhece qual loja está sendo acessada através do `Host` HTTP enviado no cabeçalho da requisição.
* **Principais Funções**:
  - Resolução de tenant dinâmica baseada em domínio.
  - Renderização automática da identidade visual da loja (logo, banner, cores, cabeçalhos, rodapés e redes sociais).
  - Estoque exclusivo da loja, permitindo buscas e detalhe do veículo.
  - Listagem de lojas e filiais relacionadas no rodapé ou no seletor de estoque.
  - Canais de captação de leads dedicados (WhatsApp e formulários locais).

### 4. `/apps/backoffice` (Painel Administrativo Nuxt)
Aplicação rica em recursos para controle de usuários e gestão do ecossistema. Funciona em dois modos baseados na role do usuário autenticado:
* **Modo Administrador Central**:
  - Criação e moderação de lojas, planos, assinaturas, domínios internos e customizados.
  - Visualização de logs de auditoria e métricas globais de performance e leads.
  - Disparadores manuais para geração de sitemaps globais e por loja.
* **Modo Administrador da Loja (Lojista)**:
  - Cadastro, edição e desativação de anúncios de veículos da sua própria loja.
  - Upload e ordenação de fotos de veículos.
  - Resposta a leads e gerenciamento da equipe/vendedores da loja.
  - Configuração visual básica da loja (logo, banners, cores e mensagens padrões do WhatsApp).
