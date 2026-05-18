# AutoHub Central - Marketplace Multi-loja de Automóveis

Este repositório é um monorepo moderno que contém as aplicações do ecossistema **AutoHub Central**, um marketplace e hub de veículos multi-tenant projetado para suportar N lojas sob domínios dinâmicos e personalizados, integrados a um portal de busca centralizado.

---

## Estrutura do Monorepo

* **`apps/api`**: Laravel API Central (Pure API-only) com PostgreSQL, Redis e isolamento multi-tenant lógico.
* **`apps/portal`**: Nuxt 4 do Marketplace Público Central.
* **`apps/storefront`**: Nuxt 4 de Sites de Lojas (Aplicação Única Dinâmica de Inquilinos).
* **`apps/backoffice`**: Nuxt 4 Administrativo (Admin Central e Lojista).
* **`packages/ui`**: Componentes e Design Tokens compartilhados do Tailwind CSS v4.
* **`packages/types`**: Interfaces TypeScript compartilhadas.

---

## Documentação do Projeto

Toda a documentação técnica detalhada está centralizada no diretório `/docs` da raiz. Para entender a arquitetura do produto, guias de desenvolvimento e padrões de código, acesse os links correspondentes abaixo:

* 📖 **[Índice Geral de Documentação](docs/README.md)** - Ponto de partida para navegação completa.
* 🚀 **[Visão Geral do Produto](docs/project-overview.md)** - Estrutura dos aplicativos e responsabilidades.
* 💻 **[Guia de Desenvolvimento Local](docs/local-development.md)** - Requisitos, configurações do Nginx local com SSL/mkcert, PostgreSQL e Redis.
* 🏛️ **[Arquitetura do Sistema](docs/architecture.md)** - Isolamento multi-tenant, infraestrutura e fluxos.
* 🌐 **[Domínios e Tenancy](docs/domains-and-tenancy.md)** - Resolução de hosts, tabela `store_domains` e cache Redis.
* 📝 **[Estilo de Código](docs/code-style.md)** - Diretrizes de codificação limpa baseadas em `UNIVERSAL-CODE-STYLE-RULES.md`.
* 🔍 **[SEO e Sitemaps](docs/seo-and-sitemaps.md)** - Geração de sitemaps via workers e rotas semânticas amigáveis.
* 🎨 **[Diretrizes de Interface (UI/UX)](docs/ui-guidelines.md)** - Visual moderno light mode com Tailwind CSS v4 e formulários discretos.
* ⚙️ **[Guia de Deployment em Produção](docs/deployment.md)** - Implantação de Nuxt na Vercel e Laravel em servidores dedicados.

---

## Regras de Execução Importantes

Tanto desenvolvedores quanto assistentes de IA devem obrigatoriamente ler o arquivo **`UNIVERSAL-CODE-STYLE-RULES.md`** contido na raiz antes de efetuar qualquer alteração no código. As diretrizes de retorno precoce (`early return`), ausência de cláusulas `else` aninhadas, tipagem forte e validação rápida de dados são estritamente exigidas neste repositório.

----

# Portal Central Agent Design Kit

This package contains a recommended agent-instruction and design-system structure for the vehicle marketplace monorepo.

## Included files

- `AGENTS.md`: root repository instructions for coding agents.
- `CLAUDE.md`: Claude Code entry instructions.
- `/apps/portal/AGENTS.md`: app-specific rules for the public marketplace portal.
- `/apps/storefront/AGENTS.md`: app-specific rules for the multi-store storefront app.
- `/apps/backoffice/AGENTS.md`: app-specific rules for the administrative backoffice.
- `/docs/design/portal/design.json`: structured design system for the public portal.
- `/docs/design/portal/design.md`: human-readable design system for the public portal.
- `/docs/design/storefront/*`: placeholder design-system files for storefront.
- `/docs/design/backoffice/*`: placeholder design-system files for backoffice.

## Recommended usage

Copy these files into the root of your monorepo.

Then ask your coding agent to read:

1. `AGENTS.md`;
2. the nearest app-level `AGENTS.md`;
3. the relevant files under `/docs/design`.

For portal UI work, the agent must read:

- `/apps/portal/AGENTS.md`
- `/docs/design/portal/design.json`
- `/docs/design/portal/design.md`

## Suggested first prompt

```md
Refactor the UI in `/apps/portal` to follow the portal design system.

Before changing files, read:

- `/AGENTS.md`
- `/apps/portal/AGENTS.md`
- `/docs/design/portal/design.json`
- `/docs/design/portal/design.md`

First inspect the current structure and produce a phased refactor plan. Do not change files yet.
```
