# Status de Implementação - Refatoração de UI/UX

Este documento apresenta a análise comparativa entre as solicitações do plano original `tasks/chats/implement-ui.md` e o estado atual da implementação no repositório.

---

## 🎯 Resumo de Conformidade

| Área / Regra | Requisito Original | Status | Observações / Detalhes de Implementação |
| :--- | :--- | :---: | :--- |
| **Geral** | Seguir o design system específico por app | **100% OK** | Arquivos `design.json` e `design.md` foram lidos e aplicados estritamente em cada app. |
| **Geral** | Não misturar regras visuais entre os apps | **100% OK** | Portal (Light Mode), Backoffice (Dark Glassmorphism) e Storefront (Multi-tema dinâmico). |
| **Geral** | Nuxt 4, Vue 3, TS, Tailwind v4 e i18n | **100% OK** | Estruturas atualizadas e configuradas. |
| **Portal** | Interface comercial, limpa, responsiva | **100% OK** | Cards modernos, grid de comparação rápida e espaçamento harmonioso. |
| **Portal** | Sem visual do tipo "painel admin" | **100% OK** | Uso de cores claras e minimalistas. |
| **Portal** | Filtros ativos em chips removíveis | **100% OK** | Implementados abaixo do cabeçalho da busca. |
| **Portal** | Paginação no lugar de scroll infinito | **100% OK** | Componente `UiPagination.vue` integrado na listagem. |
| **Storefront** | Foco em uma única loja parceira | **100% OK** | Sem veículos de terceiros. Layout isolado e customizado. |
| **Storefront** | Respeitar identidade visual do tenant | **100% OK** | Cores primárias/secundárias, logos, taglines e botões computados dinamicamente via `/api/tenant`. |
| **Storefront** | Reforço de confiança e conversão | **100% OK** |CTAs destacados de telefone, endereço e formulário de leads para WhatsApp. |
| **Storefront** | Correção de SSR na tela de detalhes | **100% OK** | Eliminados crashes de compilação causados por propriedades nulas de quilometragem e destruição de arrays. |
| **Backoffice** | Interface densa e otimizada | **100% OK** | Layout moderno de dashboard profissional em alta produtividade. |
| **Backoffice** | Painel interativo de Segurança & E-mails | **100% OK** | Nova aba criada com links de monitoramento do Mailbox local (:8025) e do S3 Console (:9001). |
| **Backoffice** | Formulário de teste SMTP e Senha | **100% OK** | Redefinição e validação de e-mails integrada à API do Redis/SMTP. |

---

## 🛠️ O que está sendo refinado agora (Sprint Atual)

Para ir além do plano e satisfazer os requisitos do item **#19** e **#21** de `detalhamento-inicial-do-projeto.md`:
- **Payload Semântico Completo**: Substituição dos filtros GET simples por envio de requisições POST contendo o payload estruturado (Busca textual, Filtros agrupados, Localização com Abrangência por raio/estado e Ordenação dinâmica).
- **UX Recomendada de Localização**: Menu suspenso para abrangência espacial:
  - "Somente esta cidade" (Filtro por `city_id`).
  - "Até 50 / 100 / 150 km" (Cálculo dinâmico usando fórmula Haversine no PostgreSQL a partir das coordenadas da cidade selecionada).
  - "Todo o estado" (Filtro por `state_id`).
- **Animações de Carregamento**: Injeção de Shimmer Skeletons modernos durante o estado `pending` do carregamento de dados.
