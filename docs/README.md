# Documentação Geral - AutoHub Central

Bem-vindo à documentação oficial do **AutoHub Central**, um marketplace multi-tenant e hub de anúncios de veículos. Este repositório está estruturado como um monorepo que contém a API central e três front-ends específicos.

Abaixo está o índice da documentação para guiar seu desenvolvimento e operação do sistema.

## Índice da Documentação

1. [Visão Geral do Projeto](project-overview.md)
   - Objetivo do produto, estrutura do monorepo e responsabilidades de cada aplicação.
2. [Desenvolvimento Local](local-development.md)
   - Requisitos locais, configuração de banco de dados PostgreSQL, Redis, SSL local com `mkcert`, proxy reverso com Nginx e mapeamento de portas.
3. [Arquitetura do Sistema](architecture.md)
   - Detalhamento de arquitetura da API central, isolamento lógico de multi-tenancy, estratégia de cache e filas de background.
4. [Domínios e Tenancy](domains-and-tenancy.md)
   - Regras de subdomínios dinâmicos das lojas, CNAME de domínios customizados, tabela de resolução e cache de host.
5. [Estilo de Código](code-style.md)
   - Explicação das regras obrigatórias de programação descritas em `UNIVERSAL-CODE-STYLE-RULES.md`.
6. [SEO e Sitemaps](seo-and-sitemaps.md)
   - Geração assíncrona de sitemaps via Jobs, rotas estratégicas de SEO de busca natural e critérios de controle.
7. [Diretrizes de Interface UI/UX](ui-guidelines.md)
   - Guia visual comercial com Tailwind CSS v4, botões, formulários discretos e internacionalização padrão para `pt-BR`.
8. [Estratégia de Deploy](deployment.md)
   - Como implantar a infraestrutura de produção (Vercel para Nuxt, servidor Nginx dedicado para Laravel API, worker, scheduler e storage).

---
*Nota: Este projeto segue estritamente as regras de Clean Code, early-return e segurança descritas na documentação e no arquivo [UNIVERSAL-CODE-STYLE-RULES.md](../UNIVERSAL-CODE-STYLE-RULES.md).*
