# Status da Tarefa: Finalizar UI/UX e lacunas funcionais do MVP

Este arquivo acompanha a execução das tarefas descritas em [tarefas-em-2026-05-19_10AM.md](file:///projects/tiago/marketplace-multi-loja-automoveis/tasks/chats/tarefas-em-2026-05-19_10AM.md).

---

## 📋 Checklist de Progresso

### 1. Portal Central (`/apps/portal`)
- [x] **Grid e Cards de Veículos**:
  - [x] Exibir de forma clara: Imagem, Título, Preço, Ano, Quilometragem, Câmbio, Combustível, Cidade/Estado, Loja.
  - [x] Implementar botão de favorito no card com feedback visual e integração ao backend.
  - [x] Adicionar estados de loading/skeleton no grid e cards.
- [x] **Filtros**:
  - [x] Filtros desktop funcionais.
  - [x] Filtros mobile em Drawer/Bottom Sheet funcional.
  - [x] Chips de filtros ativos removíveis.
- [x] **Detalhe do Veículo**:
  - [x] CTAs de contato (WhatsApp / Proposta) integrados.
  - [x] CTA "Ver no site da loja" com link correto.
- [x] **Favoritos**:
  - [x] Consolidar página `/conta/favoritos` e retirar URLs hardcoded de `localhost`.
- [x] **i18n**:
  - [x] Extrair todos os textos visíveis e hardcoded para o `pt-BR.json` do portal.

---

### 2. Storefront (`/apps/storefront`)
- [x] **Páginas Institucionais**:
  - [x] Finalizar página "Sobre" e "Contato".
  - [x] Exibir contatos, endereço, WhatsApp e horário de atendimento de forma consistente.
- [x] **Cores e Identidade Visual Dinâmica**:
  - [x] Integrar aplicação correta de HSL/cores configuradas na loja.
- [x] **i18n**:
  - [x] Extrair strings hardcoded do storefront para arquivos de tradução.
- [x] **Melhorar URLs**:
  - [x] Substituir URLs hardcoded de `localhost:7031` por chamadas via `useApi()`.

---

### 3. Backoffice (`/apps/backoffice`)
- [x] **Telas de Suporte/Help Desk**:
  - [x] Abertura de chamado, listagem de chamados e detalhes com linha do tempo.
  - [x] Distinção visual e de permissões entre administrador central e administrador da loja (lojista).
  - [x] Retirar URLs hardcoded de `localhost`.
- [x] **Configurações da Loja**:
  - [x] Criar ou finalizar tela de configurações da loja (dados básicos, identidade visual/cores, redes sociais, SEO da loja, preferences comerciais/proposta/troca).
  - [x] Restringir campos administrativos globais apenas ao administrador central.
- [x] **i18n**:
  - [x] Extrair strings administrativas e mensagens para arquivos de tradução.

---

### 4. Validação e Aceite Final
- [x] Testar fluxo completo de ponta a ponta (Criar loja -> Cadastrar veículo -> Ver no portal -> Favoritar -> Abrir chamado -> Responder chamado).
- [x] Rodar comandos de validação (`lint`, `typecheck`, `build`, `test`).
