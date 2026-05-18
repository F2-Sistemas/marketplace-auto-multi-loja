# Estratégia de Deployment - AutoHub Central

Este documento descreve as práticas recomendadas de implantação em ambiente de produção para os componentes do monorepo **AutoHub Central**.

---

## Front-ends Nuxt 4 (Portal, Storefront, Backoffice)

A infraestrutura ideal para os três aplicativos front-end em Nuxt 4 é a **Vercel** ou plataformas Serverless equivalentes, garantindo escalabilidade ilimitada de requests e distribuição via CDN global.

### 1. Configuração do Storefront Dinâmico (Multi-domínio)
Como a aplicação `storefront` atende a infinitos subdomínios e domínios customizados das lojas, a infraestrutura deve ser configurada para permitir roteamento curinga (wildcard):
* Na Vercel, associe o domínio técnico base `app-loja.rederevenda.com` habilitando a opção de subdomínios wildcard: `*.app-loja.rederevenda.com`.
* Adicione novos domínios customizados de clientes dinamicamente através da API da Vercel (Vercel Domains API) integrada ao módulo administrativo central do Backoffice, associando-os ao mesmo projeto Nuxt do storefront.
* O middleware do storefront no lado do servidor (SSR) lerá o cabeçalho de host do request original e repassará a resolução à API Laravel.

### 2. Configurações de Deploy (Vercel CLI / Git Integration)
* **apps/portal**:
  - Comando de Build: `npm run build`
  - Diretório de Saída: Padrão Nuxt (`.output`)
  - Variáveis de Ambiente: `API_URL=https://api.rederevenda.com`
* **apps/storefront**:
  - Comando de Build: `npm run build`
  - Diretório de Saída: `.output`
  - Variáveis de Ambiente: `API_URL=https://api.rederevenda.com`
* **apps/backoffice**:
  - Comando de Build: `npm run build`
  - Diretório de Saída: `.output`
  - Variáveis de Ambiente: `API_URL=https://api.rederevenda.com`

---

## Back-end Laravel API (Servidor Dedicado ou Containerizado)

Para a API Laravel, a recomendação é implantá-la em servidores dedicados (VPS, AWS EC2, DigitalOcean Droplet) ou orquestrada em containers (Docker, ECS, Kubernetes) devido às necessidades de concorrência e processos contínuos (workers de fila e crontab).

### 1. Reverse Proxy com Nginx (Produção)
Configuração enxuta e segura para expor a API Laravel sob HTTPS em produção:

```nginx
server {
    listen 80;
    server_name api.rederevenda.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name api.rederevenda.com;

    ssl_certificate /etc/letsencrypt/live/api.rederevenda.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.rederevenda.com/privkey.pem;

    root /var/www/autohub-central/apps/api/public;
    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 2. Supervisor e Workers de Fila do Redis
Para processar os jobs em background de maneira contínua, configure o utilitário **Supervisor** no servidor do Laravel para manter os workers ativos:

```ini
[program:autohub-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/autohub-central/apps/api/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/autohub-worker.log
stopwaitsecs=3600
```

### 3. Agendamento de Tarefas (Laravel Scheduler)
A geração diária e periódica de sitemaps e rotas SEO depende do Cron do sistema operacional executando o Scheduler do Laravel a cada minuto.

Adicione a seguinte entrada no Crontab do servidor:
```cron
* * * * * cd /var/www/autohub-central/apps/api && php artisan schedule:run >> /dev/null 2>&1
```

---

## Storage S3 Compatível (Imagens)

As fotos enviadas pelo painel do lojista não devem ser salvas no disco rígido local do servidor de aplicação para evitar perdas em deploys baseados em containers efêmeros e garantir rapidez de carregamento via CDN.
* Em produção, altere a variável `FILESYSTEM_DISK` de `local` para `s3` no `.env` do Laravel API.
* Utilize serviços de armazenamento compatíveis com a API S3 (AWS S3, Cloudflare R2 ou DigitalOcean Spaces).
* Vincule uma CDN (Cloudflare, AWS CloudFront) ao bucket de imagens para acelerar a renderização das fotos nas listagens de veículos das lojas e do portal.
