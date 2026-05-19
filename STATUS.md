# Status do Ecossistema - AutoHub Central (Consolidado)

Este arquivo consolida todas as atividades já concluídas, a arquitetura atual do sistema, as configurações de e-mail e armazenamento, as correções de estabilização recentes e as novas melhorias em andamento (pesquisa semântica, raio de busca e ordenação).

---

## 🚀 O que já foi realizado (Concluído)

### 💻 Backend (Laravel API Core)

1. **Modelagem de Banco de Dados**: Criadas todas as tabelas e relacionamentos necessários (Lojas, Domínios, Configurações, Usuários, Marcas, Modelos, Cidades, Veículos, Imagens, Características, Leads, Favoritos, Planos e Assinaturas).
2. **Migrations e Seeds Realistas**: Base de dados local inteiramente zerada e populada de forma coerente via `php artisan migrate:fresh --seed`. Foram criados 15 veículos dinâmicos completos vinculados a marcas, modelos, cidades reais e acessórios.
3. **Isolamento de Banco de Dados de Testes**: Configurado o arquivo `phpunit.xml` da API Laravel para direcionar os testes automatizados para a base isolada `dev_rede_revendas_marketplace_test`, evitando conflitos com os dados de desenvolvimento.
4. **Resolução Dinâmica de Tenant**: Middleware e fluxo na API Laravel para identificar o tenant ativo dinamicamente a partir do header customizado `X-Store-Host` nas requisições do storefront.
5. **Integração SMTP Mail Service & Mailables**:
   - Configuração do driver de envio de e-mails em `config/mail.php` e `.env` apontando para o servidor local de SMTP na porta `1025`.
   - Criação de templates de e-mail responsivos, modernos e elegantes alinhados ao visual premium da marca: `password-recovery.blade.php` e `email-validation.blade.php`.
   - Criação das classes de e-mail tipadas (Mailables) `PasswordRecoveryMail` e `EmailValidationMail` para suporte completo a asserções de testes.
6. **Armazenamento Escopado S3 Storage (`dev-auto-hub`)**:
   - Configuração das credenciais locais e do bucket unificado `dev-auto-hub` no arquivo `.env`.
   - Criação e registro de múltiplos discos escopados dinâmicos e isolados por ambiente (`local`, `production`, etc.) e domínio de responsabilidade:
     - `s3-uploads` $\rightarrow$ Prefixo: `[env]/uploads/` (Visibilidade: `public`)
     - `s3-videos` $\rightarrow$ Prefixo: `[env]/uploads/videos/` (Visibilidade: `public`)
     - `s3-system` $\rightarrow$ Prefixo: `[env]/system/` (Visibilidade: `private`)
     - `s3-backups` $\rightarrow$ Prefixo: `[env]/system/backups/` (Visibilidade: `private`)
7. **Controlador de Autenticação (`AuthController`)**:
   - `POST /api/auth/password/email`: Gera um código aleatório de 6 dígitos para o e-mail solicitado, armazena no Cache do Redis por 60 minutos e dispara o e-mail de recuperação para a caixa de e-mails SMTP de desenvolvimento.
   - `POST /api/auth/password/reset`: Valida o token e o e-mail contra o Cache, e atualiza a senha criptografada do usuário via `Hash::make`.
   - `POST /api/auth/email/send-verification`: Dispara o e-mail moderno de validação de conta via SMTP.
   - `POST /api/auth/email/verify`: Valida o e-mail do usuário e marca a coluna `email_verified_at` com o timestamp atual.
8. **Exceção de CSRF para API**:
   - Adicionada a regra de exclusão CSRF em `bootstrap/app.php` para todas as rotas `/api/*`. Isso resolveu o erro fatal `419 CSRF Token Mismatch` durante propostas de leads e criação de lojas.
9. **Cobertura Completa de Testes de Segurança**:
   - Suíte completa de testes automatizados criada em `tests/Feature/AuthSecurityTest.php`.
   - **100% de sucesso nos testes automatizados** via PHPUnit (`OK (24 tests, 67 assertions)`).

---

### 🌐 Frontend (Monorepo Nuxt)

#### 1. **Backoffice Central (`apps/backoffice`)**
- **Integração Real-Time com API**: Substituído todo o estado simulado por queries diretas à API Laravel.
- **CRUD de Gerenciamento de Lojas**: Listagem dinâmica de tenants de revendas parceiras com controle de ativação/desativação imediata via API.
- **Assistente de Criação de Tenant (Wizard)**: Fluxo completo integrado que insere novas concessionárias no banco de dados e configura suas propriedades visuais.
- **Painel Interativo de Segurança & E-mails**:
  - Adicionado tab dedicado **"Segurança & E-mails"** com design premium em TailwindCSS contendo campos de formulário e botões com bordas desenhadas (`rounded-lg`).
  - Seletor rápido de e-mails mockados seeded (`admin@rederevenda.com`, etc.).
  - Botões de disparo instantâneo de redefinição de senha e validação de e-mails para conferência em tempo real na mailbox SMTP.
  - Formulário completo de troca de senha contendo validação de código de 6 dígitos gerados via e-mail.
  - Cards de status informativos e links diretos para a interface web do **SMTP Mailbox (:8025)** e do **S3 Storage UI (:9001)**.

#### 2. **Portal AutoHub Marketplace (`apps/portal`)**
- **Mapeamento de Filtros e Pesquisa**: Barra de pesquisa integrada e filtros de marcas e preços consultando a API central em tempo real.
- **Formulário de Proposta / Lead**: Integrado com envio de propostas direto ao banco de dados, incluindo suporte obrigatório ao campo de **E-mail** do lead.
- **Ajuste de Navegação no Login/Admin**: Resolvido o ciclo infinito de redirecionamentos no login do portal para `/admin`.

#### 3. **Storefront das Concessionárias (`apps/storefront`)**
- **Multi-Tenancy Visual Dinâmico**: O storefront consulta `/api/tenant` injetando o header do host/domínio resolvido e adapta automaticamente toda a interface (cores de botões, gradientes, logo, telefone, WhatsApp, endereço e tagline) com base nas configurações gravadas no backend.
- **Estoque Exclusivo Filtrado**: Vitrine dinâmica que carrega somente o inventário específico e exclusivo da loja resolvida.
- **Redirecionamento WhatsApp de Leads**: Formulado envio de proposta via banco de dados e redirecionamento rápido ao WhatsApp do consultor da loja.
- **Resolução de SSR Crash na Página de Detalhes**:
  - Mapeado campo correto `vehicles` para a chave do tenant no arquivo `vehicle/[id].vue`.
  - Corrigido o acesso à propriedade `vehicle.km` substituindo pelo campo correto `vehicle.mileage` na formatação da quilometragem, eliminando falhas de SSR.

---

## 🛠️ O que está em andamento (Sprint Atual)

Estamos implementando melhorias de busca avançada baseadas na especificação oficial do projeto:
1. **Busca Semântica por Payload**: Novo endpoint `POST /api/vehicles/search` e atualização no `VehicleSearchService` para processar payloads no formato JSON semântico (busca textual, filtros estruturados, localização complexa, paginação e ordenação).
2. **Filtros de Localização Flexíveis**:
   - `"Somente esta cidade"` (Filtro simples por cidade).
   - `"Cidade e região próxima (Raio de 50km, 100km, 150km)"` (Cálculo dinâmico usando fórmula Haversine no PostgreSQL resolvendo coordenadas de cidades a partir da base).
   - `"Todo o estado"` (Filtro abrangente por estado).
3. **Ordenação no Banco de Dados**: Ajuste do fluxo de pesquisa e filtros do portal para ordenar veículos no banco de dados via campo `sort` do payload (como preço, data de criação e quilometragem).
4. **Shimmer Skeletons**: Adição de feedback visual dinâmico com shimmers modernos no grid de veículos durante o carregamento de novas buscas no portal.
5. **Suíte de Testes Adicionais**: Validação do novo serviço com `VehicleSearchPayloadTest.php`.

---

## 💡 Ações Necessárias do Usuário

Para iniciar todos os servidores integrados localmente de uma só vez:
```bash
npm run dev
```
Isso disparará os 4 serviços (API Core :8000, Portal :3000, Backoffice :3001, Storefront :3002) em paralelo com logs coloridos e reinício automático controlado!
