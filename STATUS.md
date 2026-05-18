# Status de Execução do Projeto - AutoHub Central

Este arquivo apresenta o status atualizado do desenvolvimento da primeira versão completa do ecossistema do **AutoHub Central**, incluindo as integrações de e-mail (Mailpit SMTP), armazenamento (MinIO Scoped S3), segurança e próximos passos recomendados.

---

## 🚀 O que já foi realizado (Concluído)

### 💻 Backend (Laravel API Core)
1. **Modelagem de Banco de Dados**: Criadas todas as tabelas e relacionamentos necessários (Lojas, Domínios, Configurações, Usuários, Marcas, Modelos, Cidades, Veículos, Imagens, Características, Leads, Favoritos, Planos e Assinaturas).
2. **Migrations e Seeds executados**: Base de dados local inteiramente zerada e populada de forma coerente via `php artisan migrate:fresh --seed`.
3. **Factories do Eloquent**: Criadas e implementadas factories completas para `Brand`, `VehicleModel`, `Store`, `Vehicle` e `Lead`, permitindo a geração robusta de dados para testes automatizados.
4. **Resolução Dinâmica de Tenant**: Middleware e fluxo na API Laravel para identificar o tenant ativo dinamicamente a partir do header customizado `X-Store-Host` nas requisições do storefront.
5. **Integração Mailpit SMTP & Mailables**:
   * Configuração do driver de envio de e-mails em `config/mail.php` e `.env` apontando para o servidor local SMTP do Mailpit na porta `1025`.
   * Criação de templates de e-mail responsivos, modernos e elegantes alinhados ao visual premium da marca: `password-recovery.blade.php` e `email-validation.blade.php`.
   * Criação das classes de e-mail tipadas (Mailables) `PasswordRecoveryMail` e `EmailValidationMail` para suporte completo a asserções de testes.
6. **Armazenamento Escopado MinIO (S3)**:
   * Instalação e configuração do pacote `league/flysystem-path-prefixing` para criar um disco rígido virtual de escopo restrito (`s3-uploads`).
   * Configuração das credenciais locais e endpoint do MinIO na porta `9001` no arquivo `.env`.
   * Registro do disco `s3-uploads` direcionando todos os uploads de mídias de veículos de forma isolada ao prefixo de diretório `uploads/`.
7. **Controlador de Autenticação (`AuthController`)**:
   * `POST /api/auth/password/email`: Gera um código aleatório de 6 dígitos para o e-mail solicitado, armazena no Cache do Redis por 60 minutos e dispara o e-mail de recuperação para o Mailpit.
   * `POST /api/auth/password/reset`: Valida o token e o e-mail contra o Cache, e atualiza a senha criptografada do usuário via `Hash::make`.
   * `POST /api/auth/email/send-verification`: Dispara o e-mail moderno de validação de conta via Mailpit.
   * `POST /api/auth/email/verify`: Valida o e-mail do usuário e marca a coluna `email_verified_at` com o timestamp atual.
8. **Cobertura Completa de Testes no Backend**:
   * Suíte completa de testes automatizados criada em `tests/Feature/AuthSecurityTest.php`.
   * Cobertura de **caminho feliz** (Happy Path) e **caminho triste** (Sad Path - parâmetros inválidos, validações de senha incompatíveis, usuários inexistentes).
   * **100% de sucesso nos testes automatizados** via PHPUnit (`OK (24 tests, 67 assertions)`).

### 🌐 Frontend (Monorepo Nuxt)

#### 1. **Backoffice Central (`apps/backoffice`)**
* **Integração Real-Time com API**: Substituído todo o estado simulado por queries diretas à API Laravel.
* **CRUD de Gerenciamento de Lojas**: Listagem dinâmica de tenants de revendas parceiras com controle de ativação/desativação imediata via API.
* **Assistente de Criação de Tenant (Wizard)**: Fluxo completo integrado que insere novas concessionárias no banco de dados e configura suas propriedades visuais.
* **Painel Interativo de Segurança & E-mails**:
  * Adicionado tab dedicado **"Segurança & E-mails"** com design premium em TailwindCSS contendo campos de formulário e botões com bordas desenhadas (`rounded-lg`).
  * Seletor rápido de e-mails mockados seeded (`admin@rederevenda.com`, etc.).
  * Botões de disparo instantâneo de redefinição de senha e validação de e-mails para conferência em tempo real no Mailpit.
  * Formulário completo de troca de senha contendo validação de código de 6 dígitos gerados pelo Mailpit.
  * Cards de status informativos e links diretos para a interface web do **Mailpit (:8025)** e do **MinIO (:9001)**.

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
> Você pode subir todos os serviços de uma só vez a partir do diretório raiz utilizando o nosso novo orquestrador Concurrently:
> ```bash
> npm run dev
> ```
> Isso disparará os 4 serviços (API Core :8000, Portal :3000, Backoffice :3001, Storefront :3002) em paralelo com logs coloridos e reinício automático controlado!

> [!TIP]
> ### 2. Testar e Validar os Envios de E-mail
> 1. Abra o painel do **Backoffice Central** em `http://localhost:3001/` e clique na aba **"Segurança & E-mails"** na sidebar.
> 2. Escolha o e-mail desejado no seletor e clique em **"Enviar Link de Recuperação"** ou **"Enviar Validação de E-mail"**.
> 3. Clique no botão azul **"Abrir Mailpit UI (:8025)"** no card de status à direita para abrir o Mailpit. Você verá a mensagem moderna e responsiva renderizada perfeitamente!
