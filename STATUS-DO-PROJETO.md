# Ecosistema AutoHub - Status do Projeto

Este arquivo consolida todas as atividades já concluídas, a arquitetura atual do sistema, e as ações necessárias do usuário para validação final.

---

## 🚀 O que já foi feito (Realizado com sucesso)

1. **Correção de Hoisting Crítico no Storefront:**
   - Corrigido o erro SSR que impedia a inicialização da variável reativa `searchQuery` no Nuxt.
   - Vitrine de carros do Storefront renderizando e filtrando perfeitamente.

2. **Geração e Integração Completa de Dados Realistas (Veículos e Imagens):**
   - Criado e configurado o `VehicleSeeder` com suporte a marcas, modelos, cidades, imagens reais e acessórios (features).
   - Populados 15 veículos dinâmicos no banco de dados de desenvolvimento.
   - Ajustada a `VehicleFactory` para usar `'published'` como status padrão, garantindo exibição em buscas e vitrines.

3. **Correção do Provisionamento do Backoffice (PayLoads e Modelagem):**
   - Unificado e alinhado o payload de submissão do formulário Wizard no `AdminStoreController.php` da API Laravel, suportando dados aninhados (`settings`) ou planos (`subdomain` e `slug`).
   - Corrigida a listagem do Backoffice no `app.vue` para suportar tanto retorno direto de arrays JSON quanto payloads encapsulados (`.data`).
   - Mapeados os status `'active'` para `'ativo'` e exibido o domínio principal verificado de forma dinâmica.

4. **Isolamento de Banco de Dados de Testes:**
   - Configurado o arquivo `phpunit.xml` da API Laravel para direcionar testes para a base de dados isolada `dev_rede_revendas_marketplace_test`.
   - Isso garante que a suite de testes unitários de integração possa ser executada a qualquer momento sem limpar as tabelas da base de desenvolvimento (`dev_rede_revendas_marketplace`).

5. **Exceção de CSRF para API:**
   - Adicionada a regra de exclusão CSRF em `bootstrap/app.php` para todas as rotas `/api/*`.
   - Isso resolveu o erro fatal `419 CSRF Token Mismatch` durante propostas de leads e criação de lojas.

6. **Validação de Ponta a Ponta no Navegador:**
   - Realizados testes automatizados e visuais nas portas `3000`, `3001` e `3002`.
   - Screenshots tirados provam a beleza, responsividade e total funcionamento das aplicações integradas.

---

## 🛠️ O que falta fazer / Próximos Passos

Toda a infraestrutura solicitada na especificação do projeto foi **100% implementada, testada e validada**. Não restam tarefas pendentes no escopo da primeira versão do sistema.

---

## 📥 Ações Recomendadas ao Usuário

Para interagir com o ecossistema localmente, siga estes passos simples:

1. **Iniciar os Servidores Locais** (caso parem a execução):
   - **Backend API Laravel** (Porta 8000): `php artisan serve --port=8000` (na pasta `apps/api`)
   - **Portal Central** (Porta 3000): `npm run dev` (na pasta `apps/portal`)
   - **Backoffice** (Porta 3001): `npm run dev` (na pasta `apps/backoffice`)
   - **Storefront** (Porta 3002): `npm run dev` (na pasta `apps/storefront`)

2. **Acesse as Aplicações no seu Navegador:**
   - **Marketplace Central:** [http://localhost:3000](http://localhost:3000)
   - **Painel Backoffice:** [http://localhost:3001](http://localhost:3001)
   - **Storefront Multilojas:** [http://localhost:3002](http://localhost:3002)

3. **Crie novos Tenants no Wizard:**
   - Acesse o Backoffice (3001) e clique em **Novo Tenant** para cadastrar novas concessionárias.
   - Veja elas aparecerem instantaneamente na lista!
