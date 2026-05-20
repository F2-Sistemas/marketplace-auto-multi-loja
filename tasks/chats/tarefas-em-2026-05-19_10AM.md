Sim. Pelo status descrito, o projeto está bem avançado, mas ainda existem **lacunas de MVP** que precisam virar tarefas formais antes de considerar a primeira versão fechada.

A principal lacuna não é o backend. O backend parece estar maduro. O que falta está concentrado em **fechamento de UI/UX, telas administrativas, i18n, validação visual e critérios finais de aceite**.

Abaixo está uma tarefa completa, pronta para colocar no Antigravity/Codex/Claude/AGENTS como próxima execução.

---

# Tarefa completa: Finalizar UI/UX e lacunas funcionais do MVP

## Contexto

O projeto está na fase `task/refact-ui-frontend`, com foco em consolidar a interface das três aplicações Nuxt:

* `/apps/portal`
* `/apps/storefront`
* `/apps/backoffice`

O backend Laravel API já possui os principais módulos implementados e testados: multi-tenancy, busca, favoritos, leads, help desk, estatísticas, e-mails, storage e isolamento de dados.

A etapa atual deve finalizar as pendências visuais e funcionais do MVP, garantindo que o produto fique pronto para validação de primeira versão.

As diretrizes visuais oficiais exigem UI moderna, clean, clara, comercial, responsiva, light mode, sem aparência Bootstrap, com botões discretos, inputs com foco sutil, cards brancos, bordas suaves, sombras discretas e tipografia preferencialmente Inter.

Também deve ser respeitada a regra de Tailwind CSS v4: bordas discretas, evitar rings grossos, usar `focus:ring-0` ou `focus:ring-1` discreto quando necessário, e manter foco visual elegante.

Todos os textos visíveis devem usar i18n, com `pt-BR` como idioma padrão, formatação brasileira, moeda BRL e sem textos fixos espalhados nos componentes.

---

# Objetivo da tarefa

Finalizar as lacunas de front-end do MVP, consolidando a experiência visual e funcional das três aplicações:

1. Portal central como marketplace público moderno.
2. Storefront como site individual confiável e focado na loja.
3. Backoffice como painel administrativo denso, produtivo e permission-aware.

A tarefa deve priorizar o que falta para o MVP ser usável por:

* comprador visitante;
* lojista;
* administrador central.

---

# Escopo principal

## 1. Portal central - `/apps/portal`

Finalizar e revisar:

* grid de veículos;
* cards de veículos;
* filtros desktop;
* filtros mobile em drawer/bottom sheet;
* chips de filtros ativos;
* estados de loading, empty, error e disabled;
* responsividade da listagem;
* detalhe do veículo;
* CTAs de contato;
* CTA "Ver no site da loja";
* favoritos;
* formulário de lead;
* extração de textos para i18n.

### Critérios específicos

O Portal deve parecer um marketplace público, não um painel administrativo.

A listagem deve permitir leitura rápida dos veículos, exibindo de forma clara:

* imagem;
* título;
* preço;
* ano;
* quilometragem;
* câmbio;
* combustível;
* cidade/estado;
* loja;
* botão de favorito;
* CTA principal.

### Regras de UI

* Cards brancos.
* Bordas suaves.
* Sombra discreta.
* Background geral cinza-claro.
* Botões pequenos ou médios.
* Evitar `rounded-full` como padrão.
* Evitar foco azul forte.
* Usar transições rápidas entre 150ms e 220ms.
* Mobile-first.
* Filtros mobile devem funcionar como drawer ou bottom sheet.

---

## 2. Storefront - `/apps/storefront`

Finalizar e revisar:

* home da loja;
* estoque da loja;
* detalhe do veículo;
* página "Sobre";
* página "Contato";
* identidade visual dinâmica;
* aplicação das cores da loja;
* logo;
* banners;
* dados de contato;
* WhatsApp;
* endereço;
* horários;
* unidades relacionadas, se houver;
* estados de loading, empty, error e disabled;
* extração de textos para i18n.

### Critérios específicos

O Storefront deve ser percebido como o site de uma única loja, não como uma cópia do portal central.

A experiência deve reforçar:

* confiança;
* identidade da loja;
* conversão;
* contato rápido;
* estoque exclusivo;
* clareza sobre localização e atendimento.

### Regras de produto

* Mostrar apenas veículos da loja resolvida pelo domínio.
* Não exibir veículos de outras lojas, exceto quando houver regra explícita de unidades relacionadas.
* Respeitar configurações dinâmicas da loja.
* Não mover regras críticas de negócio para o front-end.
* Não hardcodar textos.

---

## 3. Backoffice - `/apps/backoffice`

Finalizar e revisar:

* telas de suporte/help desk;
* abertura de chamado;
* listagem de chamados;
* detalhe do chamado;
* linha do tempo de mensagens;
* status/prioridade;
* telas de configurações da loja;
* dados básicos da loja;
* identidade visual;
* contatos;
* endereço;
* redes sociais;
* SEO da loja;
* preferências comerciais;
* usuários/equipe, se já houver base;
* permissões/roles na UI;
* estados de loading, empty, error e disabled;
* extração de textos para i18n.

### Critérios específicos

O Backoffice deve ser mais denso e funcional que as interfaces públicas, mas mantendo a identidade visual do projeto.

Deve priorizar:

* produtividade;
* tabelas claras;
* filtros;
* formulários objetivos;
* ações visíveis;
* feedback de sucesso/erro;
* controle de permissões;
* confirmação em ações destrutivas.

### Regras de segurança na UI

* Não exibir ações que o usuário não pode executar.
* Não permitir alteração visual de configurações que pertencem apenas ao administrador central.
* Não expor controles globais para lojistas.
* Não permitir scripts livres ou campos perigosos sem validação.

---

# Lacunas que devem ser verificadas

## A. Telas de suporte

Verificar se existem e estão completas:

* listagem de chamados;
* criação de chamado;
* detalhe do chamado;
* resposta/interação;
* alteração de status, quando permitido;
* separação entre visão do lojista e visão do admin central;
* estados vazios;
* feedback visual;
* i18n.

Se alguma tela não existir, criar.

---

## B. Telas de configuração da loja

Verificar se existem e estão completas:

* dados da loja;
* identidade visual;
* logo/banner;
* cores;
* contatos;
* WhatsApp;
* telefone;
* e-mail;
* endereço;
* horário de atendimento;
* SEO title;
* SEO description;
* mensagem padrão de WhatsApp;
* preferências de exibição de preço;
* permitir proposta;
* permitir troca;
* permitir financiamento.

O lojista pode configurar identidade, contato, site, estoque e equipe, mas não deve controlar ranking global, planos, assinaturas, destaques pagos globais, moderação, integrações globais ou SEO global do portal.

---

## C. i18n

Revisar todas as três aplicações e remover textos hardcoded visíveis.

Devem ir para arquivos de tradução:

* labels;
* placeholders;
* botões;
* mensagens de erro;
* mensagens vazias;
* títulos;
* subtítulos;
* textos de cards;
* textos de filtros;
* textos de tabelas;
* textos de modais;
* breadcrumbs;
* menus;
* tooltips;
* CTAs.

Estrutura esperada:

```text
/locales
  pt-BR.json
```

Se já houver `en-US.json`, manter compatível, mas o foco do MVP é `pt-BR`.

---

## D. Estados de interface

Garantir que os principais fluxos tenham:

* loading;
* empty state;
* error state;
* disabled state;
* hover;
* active;
* focus;
* skeleton quando fizer sentido;
* mensagem clara quando não houver dados;
* retry quando houver falha de carregamento.

---

## E. Responsividade

Validar:

* portal em mobile;
* filtros mobile;
* cards em mobile;
* detalhe do veículo em mobile;
* storefront em mobile;
* CTAs de contato em mobile;
* backoffice em telas menores;
* tabelas administrativas com overflow adequado;
* menus laterais;
* drawers;
* modais.

---

## F. Critérios finais de aceite do MVP

Validar se é possível executar o fluxo completo:

1. Criar uma loja no backoffice central.
2. Gerar automaticamente domínio interno.
3. Acessar site da loja pelo domínio interno.
4. Cadastrar veículo pela loja.
5. Enviar imagens do veículo.
6. Visualizar veículo no Storefront.
7. Visualizar veículo no Portal.
8. Abrir detalhe completo do veículo dentro do Portal.
9. Ver outros veículos da mesma loja dentro do Portal.
10. Clicar em "Ver no site da loja".
11. Gerar lead/contato.
12. Favoritar veículo como visitante logado.
13. Abrir chamado de suporte como lojista.
14. Visualizar/responder chamado como admin central, se implementado.
15. Alterar configurações básicas da loja.
16. Ver alteração refletida no Storefront.
17. Manter isolamento correto entre lojas.
18. Rodar build/lint/typecheck sem erros novos.

Esses critérios estão alinhados com a definição de pronto do projeto, que exige portal, storefront, backoffice, domínios, tenant, SEO/sitemap, UI/UX e isolamento funcionando antes da primeira versão.

---

# Fora do escopo desta tarefa

Não implementar agora:

* comparador gráfico lado a lado;
* Meilisearch/Elasticsearch/OpenSearch;
* PostGIS, salvo necessidade real;
* microserviços;
* deploy por loja;
* banco separado por loja;
* social login;
* integrações completas com portais externos;
* automações comerciais avançadas;
* recomendação inteligente.

O comparador avançado de veículos deve permanecer como pós-MVP.

---

# Ordem recomendada de execução

## Fase 1 - Auditoria rápida

Antes de alterar arquivos:

1. Ler `STATUS.md`.
2. Ler `implement-ui.md`.
3. Ler `implement-ui-status.md`.
4. Verificar `git status`.
5. Mapear arquivos modificados.
6. Identificar telas incompletas.
7. Identificar textos hardcoded.
8. Identificar componentes duplicados ou desalinhados.

Gerar um plano curto antes de codificar.

---

## Fase 2 - Portal

Priorizar:

1. `VehicleCard.vue`.
2. `VehicleGrid.vue`.
3. filtros mobile.
4. chips de filtros ativos.
5. detalhe do veículo.
6. estados de UI.
7. i18n.

---

## Fase 3 - Storefront

Priorizar:

1. home da loja;
2. estoque;
3. detalhe do veículo;
4. contato;
5. sobre;
6. identidade dinâmica;
7. CTAs de WhatsApp;
8. estados de UI;
9. i18n.

---

## Fase 4 - Backoffice

Priorizar:

1. suporte/help desk;
2. configurações da loja;
3. formulários administrativos;
4. permissões na UI;
5. estados de UI;
6. i18n.

---

## Fase 5 - Validação final

Executar comandos disponíveis no monorepo:

```bash
git status
```

```bash
pnpm lint
```

```bash
pnpm typecheck
```

```bash
pnpm test
```

```bash
pnpm build
```

Se os comandos forem por app, executar equivalentes em:

```bash
/apps/portal
/apps/storefront
/apps/backoffice
```

Se algum comando não existir, reportar claramente.

Se falhar por erro pré-existente, reportar.

Se falhar por alteração da tarefa, corrigir.

---

# Critérios de pronto da tarefa

A tarefa só deve ser considerada concluída quando:

* Portal estiver visualmente consistente e responsivo.
* Storefront estiver respeitando identidade da loja.
* Backoffice tiver telas de suporte e configurações utilizáveis.
* Não houver textos visíveis hardcoded relevantes.
* Estados de loading/empty/error/disabled existirem nos fluxos principais.
* Filtros mobile do Portal estiverem funcionais.
* Cards e grids estiverem alinhados ao design system.
* Lojista conseguir alterar configurações permitidas.
* Admin central conseguir operar suporte/configurações necessárias.
* Build/typecheck/lint não apresentarem erros novos.
* `git status` estiver claro e pronto para commit.

---

# Prompt pronto para executar no agente

```md
You are working on the AutoHub Central monorepo.

Current status:
The backend Laravel API core is mostly complete and tested. The project is now in the UI/UX refactor and consolidation phase for the three Nuxt front-end apps:

- /apps/portal
- /apps/storefront
- /apps/backoffice

Your task is to finish the remaining MVP front-end gaps and consolidate the UI according to each app design system.

Read first:

- STATUS.md
- implement-ui.md
- implement-ui-status.md
- AGENTS.md
- CLAUDE.md
- /apps/portal/AGENTS.md if present
- /apps/storefront/AGENTS.md if present
- /apps/backoffice/AGENTS.md if present
- /docs/design/portal/design.json if present
- /docs/design/portal/design.md if present
- /docs/design/storefront/design.json if present
- /docs/design/storefront/design.md if present
- /docs/design/backoffice/design.json if present
- /docs/design/backoffice/design.md if present

Before coding:

1. Run git status.
2. Inspect modified files.
3. Inspect current pages/components/layouts.
4. Identify incomplete UI flows.
5. Identify hardcoded visible strings.
6. Identify missing loading, empty, error and disabled states.
7. Produce a short execution plan.

Main goals:

1. Finish Portal vehicle cards, vehicle grid, filters, mobile filters, active filter chips, vehicle detail CTAs, lead/contact UI and i18n.
2. Finish Storefront home, inventory, vehicle detail, about, contact, dynamic store identity, WhatsApp CTAs, loading/empty/error states and i18n.
3. Finish Backoffice support/help desk screens, store settings screens, permission-aware actions, administrative forms, loading/empty/error states and i18n.

Global rules:

- Use Nuxt 4, Vue 3 Composition API, TypeScript and Tailwind CSS v4.
- Use i18n for all visible text.
- Do not hardcode visible strings in components.
- Do not change API contracts unless strictly necessary.
- Do not move critical business logic to the front-end.
- Do not rewrite unrelated code.
- Keep the MVP simple, clean, responsive and production-ready.
- Preserve existing routes unless a clear bug requires adjustment.
- Preserve existing data flow unless there is a clear bug.
- Add loading, empty, error, disabled, hover, active and focus states where applicable.
- Avoid Bootstrap-like UI.
- Avoid thick focus rings.
- Avoid rounded-full as the default button style.
- Prefer subtle borders, white cards, discreet shadows and light gray backgrounds.
- Use light mode as the default.
- Use Inter or the configured project font.

Portal-specific rules:

- /apps/portal must look like a modern public vehicle marketplace.
- It must not look like an admin panel.
- Vehicle cards must show image, title, price, metadata, store, city/state and favorite action.
- Vehicle listing must support fast scanning.
- Mobile filters must use drawer or bottom sheet behavior.
- Active filters must appear as removable chips.
- Vehicle detail must prioritize photos, price, contact CTA and "View on store website".
- Prefer pagination over infinite scroll for the MVP.

Storefront-specific rules:

- /apps/storefront must look like a public website for one store.
- It must respect store settings: logo, colors, banners, contacts, address, SEO fields, featured vehicles and theme settings.
- It must reinforce trust and conversion.
- It must show only vehicles from the resolved store.
- Do not show unrelated stores unless related units are explicitly supported.
- Do not make it look like the central marketplace unless the design system explicitly says so.
- Do not make it look like backoffice.

Backoffice-specific rules:

- /apps/backoffice must be dense, clear and functional.
- It must support central admin and store admin contexts.
- It must prioritize productivity, tables, forms, filters, actions and permissions.
- Do not expose unauthorized actions in the UI.
- Keep destructive actions explicit and confirmable.
- Finish support/help desk screens.
- Finish store settings screens.
- Do not make it look like a public marketplace or marketing website.

Required MVP flows to validate:

1. Create a store in central backoffice.
2. Access the store by its internal domain.
3. Create a vehicle as store admin.
4. Upload vehicle images.
5. View the vehicle in the storefront.
6. View the vehicle in the portal.
7. Open vehicle detail inside the portal.
8. Generate a lead/contact.
9. Favorite a vehicle as a logged visitor.
10. Open a support ticket as store admin.
11. View/respond to support ticket as central admin if supported.
12. Update allowed store settings.
13. See store settings reflected in storefront.
14. Confirm tenant isolation remains correct.

After implementation:

Run available validation commands:

- lint
- typecheck
- tests
- build

If commands do not exist, report that.
If commands fail because of existing issues, report clearly.
If commands fail because of your changes, fix them.

Final response must include:

1. Summary of changed files.
2. What was completed in /apps/portal.
3. What was completed in /apps/storefront.
4. What was completed in /apps/backoffice.
5. i18n changes.
6. Validation commands executed.
7. Remaining risks or follow-up tasks.
8. Whether the repository is ready for commit.
```

---

# Resumo objetivo do que está faltando

Falta transformar o projeto de "funcional tecnicamente" para **MVP operável com interface fechada**.

As pendências mais importantes são:

1. **Portal:** cards, grid, filtros mobile, chips, estados e i18n.
2. **Storefront:** telas institucionais, identidade dinâmica refinada, contato/conversão e i18n.
3. **Backoffice:** suporte/help desk e configurações da loja.
4. **Geral:** estados de interface, responsividade, textos traduzidos e validação final.
5. **Aceite:** testar fluxo completo de loja -> veículo -> portal -> storefront -> lead -> configurações -> suporte.

O comparador lado a lado pode continuar fora do MVP sem problema. Isso está coerente com a diretriz de evitar features avançadas antes do essencial funcionar.
