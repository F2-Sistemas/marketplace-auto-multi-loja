# Requisitos e Desenvolvimento Local - AutoHub Central

Esta seção detalha o ecossistema local e como executar as aplicações para desenvolvimento.

---

## Requisitos de Ambiente Local

* **PHP 8.3+** com extensões instaladas: `pdo_pgsql`, `redis`, `bcmath`, `xml`, `mbstring`, `zip`, `xdebug` (opcional).
* **Composer 2.2+** para dependências do back-end.
* **Node.js v22+** e **NPM v11+** para desenvolvimento dos front-ends Nuxt 4.
* **PostgreSQL (v15 ou superior)** rodando localmente ou em container Docker.
* **Redis** rodando localmente ou em container Docker.
* **Nginx** local configurado como proxy reverso.
* **mkcert** para geração de certificados SSL locais confiáveis.

---

## Banco de Dados Local (PostgreSQL)

O banco de dados deve utilizar as seguintes credenciais e configurações de conexão de desenvolvimento:

```env
DATABASE_CONNECTION=pgsql
DATABASE_HOST=172.17.0.1
DATABASE_PORT=1010
DATABASE_USERNAME=postgres
DATABASE_PASSWORD=postgres
DATABASE_DATABASE=dev_rede_revendas_marketplace
```

Para rodar a suite de testes automatizados locais, o banco de dados dedicado é:

```env
DATABASE_TEST_DATABASE=dev_rede_revendas_marketplace_test
```

---

## Cache e Filas Local (Redis)

O Redis de desenvolvimento deve ser configurado com:

```env
REDIS_HOST=172.17.0.1
REDIS_PORT=1020
REDIS_PASSWORD=
```

---

## Domínios Técnicos Locais

O sistema utiliza estes domínios técnicos locais configurados no arquivo `/etc/hosts` de sua máquina:

```text
127.0.0.1 api.rederevenda.com
127.0.0.1 rederevenda.com
127.0.0.1 admin.rederevenda.com
127.0.0.1 app-loja.rederevenda.com
127.0.0.1 loja01.rederevenda.com
127.0.0.1 loja02.rederevenda.com
```

---

## Portas Locais das Aplicações

Cada aplicação do monorepo roda nas seguintes portas locais em ambiente de desenvolvimento:

| Aplicação | Domínio Local Técnico | Porta |
| :--- | :--- | :--- |
| **Portal Central (Nuxt)** | `rederevenda.com` | `7030` |
| **API Laravel (Back-end)** | `api.rederevenda.com` | `7031` |
| **Backoffice (Nuxt)** | `admin.rederevenda.com` | `7032` |
| **Storefront (Nuxt)** | `app-loja.rederevenda.com` | `7033` |

---

## Configuração do Proxy Reverso Nginx Local (SSL com mkcert)

Configure um bloco do servidor Nginx local para atuar como proxy com HTTPS configurado utilizando certificados válidos gerados pelo `mkcert`:

```nginx
# Portal Central Marketplace
server {
    listen 443 ssl;
    server_name rederevenda.com;

    ssl_certificate /caminho/para/rederevenda.com.pem;
    ssl_certificate_key /caminho/para/rederevenda.com-key.pem;

    location / {
        proxy_pass http://localhost:7030;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}

# API Laravel
server {
    listen 443 ssl;
    server_name api.rederevenda.com;

    ssl_certificate /caminho/para/api.rederevenda.com.pem;
    ssl_certificate_key /caminho/para/api.rederevenda.com-key.pem;

    location / {
        proxy_pass http://localhost:7031;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}

# Backoffice
server {
    listen 443 ssl;
    server_name admin.rederevenda.com;

    ssl_certificate /caminho/para/admin.rederevenda.com.pem;
    ssl_certificate_key /caminho/para/admin.rederevenda.com-key.pem;

    location / {
        proxy_pass http://localhost:7032;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}

# Storefront dinâmico das lojas
server {
    listen 443 ssl;
    server_name app-loja.rederevenda.com *.app-loja.rederevenda.com loja01.rederevenda.com loja02.rederevenda.com;

    ssl_certificate /caminho/para/wildcard.app-loja.rederevenda.com.pem;
    ssl_certificate_key /caminho/para/wildcard.app-loja.rederevenda.com-key.pem;

    location / {
        proxy_pass http://localhost:7033;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

---

## Como Executar as Aplicações Localmente

### 1. Rodar a API Laravel
```bash
cd apps/api
composer install
cp .env.example .env # preencha as chaves de banco/redis
php artisan key:generate
php artisan migrate --seed
php artisan serve --port=7031
```

### 2. Rodar o Worker de Filas da API (Em um terminal separado)
```bash
cd apps/api
php artisan queue:work
```

### 3. Rodar os Front-ends Nuxt 4 (Usar shells interativos para carregar o Node/NPM adequados)
No seu terminal local, execute:

```bash
# Executar Portal Central (porta 7030)
cd apps/portal
npm install
npm run dev -- --port 7030

# Executar Backoffice (porta 7032)
cd apps/backoffice
npm install
npm run dev -- --port 7032

# Executar Storefront Dinâmico (porta 7033)
cd apps/storefront
npm install
npm run dev -- --port 7033
```
