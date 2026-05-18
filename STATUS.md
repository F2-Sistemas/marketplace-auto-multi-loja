# Status de Execução do Projeto - AutoHub Central

Este arquivo apresenta o status atualizado do desenvolvimento da primeira versão completa do ecossistema do **AutoHub Central**, incluindo as integrações realizadas e próximos passos recomendados.

---

## 🚀 O que já foi realizado (Concluído)

### 💻 Backend (Laravel API Core)
1. **Modelagem de Banco de Dados**: Criadas todas as tabelas e relacionamentos necessários (Lojas, Domínios, Configurações, Usuários, Marcas, Modelos, Cidades, Veículos, Imagens, Características, Leads, Favoritos, Planos e Assinaturas).
2. **Migrations e Seeds executados**: Base de dados local inteiramente zerada e populada de forma coerente via `php artisan migrate:fresh --seed`.
3. **Factories do Eloquent**: Criadas e implementadas factories completas para `Brand`, `VehicleModel`, `Store`, `Vehicle` e `Lead`, permitindo a geração robusta de dados para testes automatizados.
4. **Resolução Dinâmica de Tenant**: Implementado middleware e fluxo na API Laravel para identificar o tenant ativo dinamicamente a partir do header customizado `X-Store-Host` nas requisições do storefront.
5. **Rotas e Controladores da API**:
   * `GET /api/tenant`: Retorna as configurações completas e informações visuais da loja a partir do host/subdomínio resolvido.
   * `GET /api/vehicles`: Lista o inventário exclusivo de automóveis do tenant ativo (suporta query de pesquisa `?q=`).
   * `POST /api/leads`: Valida e insere novas propostas de interesse vinculadas a veículos no banco de dados.
   * `GET /api/admin/stores`: Retorna a listagem completa de tenants cadastrados para o painel do backoffice central.
   * `POST /api/admin/stores`: Provisiona e ativa instantaneamente novos tenants no banco de dados.
   * `POST /api/admin/stores/{id}/toggle`: Alterna proativamente o status de ativação de lojas parceiras.

### 🌐 Frontend (Monorepo Nuxt)

#### 1. **Backoffice Central (`apps/backoffice`)**
* **Integração Real-Time com API**: Substituído todo o estado simulado por queries diretas à API Laravel.
* **Métricas do Painel Central**: Exibição em tempo real do faturamento geral acumulado e contadores de lojas e veículos.
* **CRUD de Gerenciamento de Lojas**: Listagem dinâmica de tenants de revendas parceiras com controle de ativação/desativação imediata via API.
* **Assistente de Criação de Tenant (Wizard)**: Fluxo completo integrado que insere novas concessionárias no banco de dados e configura suas propriedades visuais.

#### 2. **Portal AutoHub Marketplace (`apps/portal`)**
* **Mapeamento de Filtros e Pesquisa**: Barra de pesquisa integrada e filtros de marcas e preços consultando a API central em tempo real.
* **Formulário de Proposta / Lead**: Integrado com envio de propostas direto ao banco de dados, incluindo suporte obrigatório ao campo de **E-mail** do lead.

#### 3. **Storefront das Concessionárias (`apps/storefront`)**
* **Multi-Tenancy Visual Dinâmico**: O storefront consulta `/api/tenant` injetando o header do host/domínio resolvido e adapta automaticamente toda a interface (cores de botões, gradientes, logo, telefone, WhatsApp, endereço e tagline) com base nas configurações gravadas no backend.
* **Estoque Exclusivo Filtrado**: Vitrine dinâmica que carrega somente o inventário específico e exclusivo da loja resolvida.
* **Redirecionamento WhatsApp de Leads**: Formulado envio de proposta via banco de dados e redirecionamento rápido ao WhatsApp do consultor da loja.

---

## 📋 O que falta fazer (Próximos Passos recomendados)

1. **Testes de Integração Ponta a Ponta**: Validar o comportamento do subdominío sob apontamentos reais de DNS (ex: `natal-motors.rederevenda.com` resolvendo no servidor Nginx/Apache local).
2. **Integração com Gateway de Pagamentos**: Implementar a cobrança real de mensalidades e planos no fluxo de criação de lojas do Backoffice.
3. **Upload Real de Fotos dos Carros**: Conectar o envio de mídias de veículos à API do AWS S3 ou armazenamento local estruturado.

---

## 💡 Ações Necessárias do Usuário (Sua Ação)

Para prosseguir com os testes visuais e validações locais, siga os passos abaixo:

> [!NOTE]
> ### 1. Subir os servidores locais
> Execute o servidor de desenvolvimento da API Laravel (Porta 8000) e os apps Nuxt (Portas 3000 para portal, 3001 para backoffice, 3002 para storefront).
> ```bash
> # Terminal 1 (API Laravel)
> cd apps/api
> php artisan serve --port=8000
> 
> # Terminal 2 (Monorepo Nuxt)
> npm run dev
> ```

> [!TIP]
> ### 2. Testar a Multi-Tenancy no Navegador
> O seletor flutuante de **Simulador de Tenant** localizado no canto inferior direito do **Storefront** e do **Portal** permite que você alterne o domínio virtual para ver o design temático e o inventário mudarem instantaneamente em tempo real!
