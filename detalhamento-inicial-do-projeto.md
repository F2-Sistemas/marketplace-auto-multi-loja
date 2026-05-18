Abaixo está o documento completo e atualizado, já incorporando a regra correta dos domínios dinâmicos das lojas.

# Documento base do projeto - AutoHub Central

## 1. Nome provisório do projeto

Nome técnico provisório:

**AutoHub Central**

Esse nome é apenas um pseudônimo interno até a definição do nome comercial final.

---

# 2. Objetivo do projeto

Criar um portal central de veículos, no modelo marketplace/hub, onde várias lojas e revendas de automóveis possam cadastrar seus veículos e ter seus próprios sites individuais, enquanto todos os anúncios também ficam disponíveis em um portal central público.

O sistema deve permitir:

* portal central público para busca de veículos de várias lojas;
* site individual para cada loja/revenda;
* backoffice para administração central e administração da loja;
* cadastro de veículos com imagens, dados técnicos e comerciais;
* leads, favoritos e contato com a loja;
* SEO forte para veículos, lojas, cidades, marcas, modelos e rotas estratégicas;
* geração de sitemaps para portal central e lojas;
* estrutura multi-tenant simples, profissional e pronta para produção.

---

# 3. Regra principal do projeto

O projeto deve ser planejado antes da execução.

Nenhuma implementação importante deve ser iniciada sem antes definir:

* estrutura do monorepo;
* arquitetura da API;
* estrutura dos front-ends;
* modelo de dados;
* fluxo de criação de loja;
* resolução de domínio;
* regras de tenant;
* estratégia de cache;
* regras de SEO;
* critérios de pronto.

A execução deve priorizar MVP funcional, com arquitetura limpa e sem excesso de engenharia.

---

# 4. Stack definida

## Back-end

* Laravel API only.
* API-first.
* PostgreSQL.
* Redis.
* Queues/jobs.
* Policies.
* Form Requests.
* API Resources.
* Services/Actions quando fizer sentido.
* Migrations bem estruturadas.
* Testes onde fizer sentido.

## Front-end

* Nuxt 4 para o portal central.
* Nuxt 4 para o storefront das lojas.
* Nuxt 4 para o backoffice.
* Vue 3 Composition API.
* Tailwind CSS v4.
* i18n com `pt-BR` como idioma padrão.

## Infraestrutura

* PostgreSQL como banco principal.
* Redis para cache e filas.
* Storage S3 compatível para imagens.
* Nginx para proxy/reverse proxy.
* SSL local com mkcert no ambiente de desenvolvimento.
* Deploy sem criar uma aplicação por loja.

---

# 5. Estrutura do monorepo

Estrutura recomendada:

```text
/apps
  /api
  /portal
  /storefront
  /backoffice

/packages
  /ui
  /types
```

## Responsabilidade de cada app

### `/apps/api`

Laravel API central.

Responsável por:

* autenticação;
* tenants/lojas;
* domínios;
* configurações;
* veículos;
* imagens;
* leads;
* favoritos;
* conversas;
* usuários;
* permissões;
* planos;
* assinaturas;
* busca;
* SEO;
* sitemap;
* integrações futuras;
* auditoria.

### `/apps/portal`

Nuxt 4 do portal central público.

Responsável por:

* home do marketplace;
* busca global;
* listagem de veículos;
* detalhe do veículo dentro do portal;
* página pública da loja dentro do portal;
* outros veículos da mesma loja;
* favoritos;
* login/cadastro do visitante;
* contato/leads;
* CTA "Ver no site da loja";
* SEO do portal central.

### `/apps/storefront`

Nuxt 4 dos sites das lojas.

Uma única aplicação atende N lojas.

Responsável por:

* resolver loja pelo domínio/host;
* carregar configurações da loja;
* renderizar tema/layout da loja;
* home da loja;
* estoque;
* detalhe do veículo;
* sobre;
* contato;
* unidades relacionadas;
* SEO da loja.

### `/apps/backoffice`

Nuxt 4 administrativo.

Responsável por:

* administração central;
* administração da loja;
* gestão de lojas;
* gestão de veículos;
* gestão de imagens;
* gestão de leads;
* gestão de usuários;
* permissões;
* configurações;
* planos;
* assinaturas;
* domínios;
* SEO;
* sitemap;
* relatórios;
* auditoria.

### `/packages/ui`

Opcional.

Pode conter componentes compartilhados entre portal, storefront e backoffice.

### `/packages/types`

Opcional.

Pode conter tipos TypeScript, contratos e estruturas compartilhadas.

---

# 6. Decisões arquiteturais

## Deve fazer

* Usar Laravel como API central.
* Usar Nuxt 4 nos três front-ends principais.
* Usar uma única aplicação storefront para todas as lojas.
* Resolver loja pelo domínio recebido na requisição.
* Usar banco central PostgreSQL.
* Usar `store_id` nas tabelas operacionais.
* Usar Redis para cache.
* Usar jobs para tarefas pesadas.
* Usar PostgreSQL para busca no MVP.
* Preparar SearchService para futura troca por Meilisearch/Elasticsearch/OpenSearch.

## Não deve fazer no MVP

* Não criar microserviços.
* Não criar deploy por loja.
* Não criar banco separado por loja.
* Não criar schema separado por loja.
* Não colocar regras de negócio críticas no front-end.
* Não usar busca externa antes de realmente precisar.
* Não criar complexidade comercial avançada antes do essencial funcionar.

---

# 7. Multi-tenancy

O projeto deve usar abordagem multi-tenant com banco central.

A API deve usar `store_id` ou `tenant_id` nas tabelas operacionais.

O pacote `tenancy for Laravel` pode ser usado para facilitar:

* contexto do tenant;
* cache tenant-aware;
* storage tenant-aware;
* escopos globais quando fizer sentido;
* resolução de tenant/domínio.

Mesmo usando o pacote, o projeto deve manter `store_id` explícito nas tabelas operacionais importantes.

Exemplos:

* `vehicles.store_id`
* `vehicle_images.store_id`
* `leads.store_id`
* `store_users.store_id`
* `favorites.store_id`, quando aplicável
* `conversations.store_id`

---

# 8. Domínios das lojas

## Regra principal

As lojas não devem ficar configuradas em variáveis de ambiente.

Os domínios das lojas devem ser dinâmicos e armazenados no banco de dados.

O `.env` deve conter apenas domínios técnicos/base da aplicação.

Exemplo:

```env
LOCAL_API_DOMAIN=api.rederevenda.com
LOCAL_PORTAL_DOMAIN=rederevenda.com
LOCAL_BACKOFFICE_DOMAIN=admin.rederevenda.com
LOCAL_STOREFRONT_BASE_DOMAIN=site.rederevenda.com
```

## Domínio interno da loja

Toda loja deve possuir automaticamente um domínio interno principal no formato:

```text
{public_id}.site.rederevenda.com
```

O `public_id` não deve ser numérico puro.

Ele deve ser uma string única, amigável ou aleatória.

Exemplos:

```text
autocar-pedro-natal-rn
abcdefghijkl
rrv-8xk29p
```

Exemplos de domínio interno:

```text
autocar-pedro-natal-rn.site.rederevenda.com
abcdefghijkl.site.rederevenda.com
rrv-8xk29p.site.rederevenda.com
```

## Domínios customizados

Uma loja pode ter domínios customizados vinculados.

Exemplo:

```text
dominio-custom.com -> CNAME -> autocar-pedro-natal-rn.site.rederevenda.com
```

A aplicação deve resolver a loja pelo `host` recebido na requisição.

O domínio customizado deve ser validado antes de ficar ativo.

---

# 9. Modelo de domínios

## `stores`

Campos importantes:

```text
id
public_id
name
slug
status
created_at
updated_at
```

## `store_domains`

Campos importantes:

```text
id
store_id
domain
type
is_primary
is_verified
verified_at
status
created_at
updated_at
```

Tipos possíveis:

```text
internal
custom
```

Status possíveis:

```text
pending
active
inactive
failed
```

Exemplo de domínio interno:

```text
store_id: 10
domain: autocar-pedro-natal-rn.site.rederevenda.com
type: internal
is_primary: true
is_verified: true
status: active
```

Exemplo de domínio customizado:

```text
store_id: 10
domain: dominio-custom.com
type: custom
is_primary: false
is_verified: false
status: pending
```

---

# 10. Resolução de loja por domínio

A aplicação storefront deve:

1. receber a requisição;
2. ler o `host`;
3. consultar/cachear `store_domains`;
4. identificar a loja;
5. carregar configurações da loja;
6. renderizar o site correto.

Cache recomendado:

```text
store:domain:{domain}
store:{id}:settings
store:{id}:navigation
store:{id}:featured_vehicles
```

Se o domínio não for encontrado, a aplicação deve retornar página adequada de erro ou página institucional de domínio não configurado.

---

# 11. Fluxo de criação de loja

Fluxo recomendado:

1. Gestor central cria a loja.
2. Sistema gera `public_id`.
3. Sistema cria domínio interno principal:

```text
{public_id}.site.rederevenda.com
```

4. Sistema salva o domínio em `store_domains`.
5. Domínio interno fica como:

   * `type = internal`
   * `is_primary = true`
   * `is_verified = true`
   * `status = active`
6. Gestor informa dados básicos da loja.
7. Gestor define plano.
8. Gestor define tema inicial.
9. Gestor cria usuário administrador da loja.
10. Sistema salva configurações iniciais.
11. Loja fica pronta.
12. Site da loja já fica acessível pelo domínio interno.
13. Domínios customizados podem ser adicionados e verificados depois.

---

# 12. Configurações da loja

O lojista pode configurar:

## Identidade

* nome fantasia;
* logo;
* banner;
* cores;
* layout/tema;
* slogan;
* descrição;
* redes sociais.

## Contato

* WhatsApp;
* telefone;
* e-mail;
* endereço;
* bairro;
* cidade;
* estado;
* latitude/longitude;
* horário de atendimento;
* link do mapa.

## Site

* SEO title;
* SEO description;
* banners da home;
* veículos em destaque;
* texto "sobre";
* mensagem padrão de WhatsApp.

## Estoque

* exibir preço;
* exibir "sob consulta";
* permitir proposta;
* permitir troca;
* permitir financiamento;
* destacar veículos específicos.

## Equipe

* usuários da loja;
* vendedores;
* permissões internas.

---

# 13. Configurações que o lojista não deve controlar

O lojista não deve controlar:

* ranking global do portal;
* planos;
* assinaturas;
* limites comerciais;
* destaques pagos globais;
* regras de moderação;
* campos obrigatórios globais;
* integrações globais;
* scripts livres sem validação;
* SEO global do portal.

---

# 14. Configurações do administrador central

O administrador central controla:

* lojas;
* planos;
* assinaturas;
* limites por plano;
* status da loja;
* domínios;
* moderação de anúncios;
* banners do portal;
* destaques pagos;
* lojas patrocinadas;
* SEO global;
* cidades/marcas em destaque;
* relatórios;
* integrações externas;
* auditoria;
* relacionamento entre lojas.

---

# 15. Relacionamento entre lojas

Algumas lojas podem ter relacionamento entre si.

Tipos possíveis:

```text
matriz
filial
grupo
parceira
```

No MVP, isso não deve criar regras complexas.

Usos iniciais:

* exibir unidades relacionadas no rodapé do site da loja;
* permitir filtro "incluir unidades relacionadas";
* mostrar outras unidades da mesma rede;
* melhorar navegação entre lojas relacionadas.

---

# 16. Portal central

O portal central deve permitir que o visitante veja anúncios sem precisar sair do portal.

Páginas essenciais:

* home;
* busca/listagem;
* detalhe do veículo;
* página pública da loja;
* outros veículos da mesma loja;
* favoritos;
* login/cadastro;
* contato/leads;
* termos;
* privacidade.

## Detalhe do veículo no portal

Deve exibir:

* fotos;
* título;
* preço;
* ano;
* quilometragem;
* câmbio;
* combustível;
* opcionais;
* descrição;
* dados da loja;
* outros veículos da loja;
* veículos similares;
* CTA WhatsApp;
* CTA telefone;
* CTA "Ver no site da loja".

Regra importante:

O visitante deve conseguir ver o anúncio, a loja e outros anúncios da loja dentro do portal central, sem ser obrigado a sair para o site da loja.

O botão "Ver no site da loja" deve existir como opção visível.

---

# 17. Site da loja

O site da loja deve conter:

* home;
* estoque;
* detalhe do veículo;
* sobre;
* contato;
* unidades relacionadas;
* SEO da loja.

A listagem deve exibir apenas veículos da loja.

No futuro, pode permitir incluir veículos de unidades relacionadas.

---

# 18. Backoffice

O backoffice deve ser uma aplicação Nuxt 4 separada.

Deve atender dois contextos:

* administrador central;
* administrador da loja.

A separação deve ser feita por permissões e roles.

## Administração central

Deve permitir:

* criar lojas;
* gerenciar lojas;
* gerenciar domínios;
* gerenciar planos;
* gerenciar assinaturas;
* gerenciar banners do portal;
* gerenciar destaques;
* moderar anúncios;
* configurar SEO global;
* gerenciar relacionamentos entre lojas;
* visualizar relatórios;
* acessar auditoria;
* disparar geração de sitemap.

## Administração da loja

Deve permitir:

* gerenciar veículos;
* enviar imagens;
* gerenciar leads;
* configurar dados da loja;
* configurar aparência do site;
* configurar contatos;
* gerenciar usuários/equipe;
* gerenciar permissões internas permitidas.

---

# 19. Busca e filtros

## Payload padrão

O payload de busca deve ser semântico, expansível e organizado por contexto.

```json
{
  "search": {
    "term": "corolla"
  },
  "location": {
    "mode": "radius",
    "city_id": 2408102,
    "radius_km": 50,
    "include_related_units": false
  },
  "filters": {
    "brand_id": 1,
    "model_id": 20,
    "price": {
      "min": 50000,
      "max": 120000
    },
    "year": {
      "min": 2018,
      "max": 2024
    },
    "mileage": {
      "max": 80000
    },
    "fuel": ["flex", "gasoline"],
    "transmission": ["automatic"],
    "body_type": ["sedan", "suv"]
  },
  "sort": {
    "field": "created_at",
    "direction": "desc"
  },
  "pagination": {
    "page": 1,
    "per_page": 24
  }
}
```

## Regra do payload

* `search`: texto e contexto da busca.
* `location`: localização e abrangência.
* `filters`: filtros técnicos/comerciais.
* `sort`: ordenação.
* `pagination`: paginação.
* arrays: múltipla seleção.
* objetos: intervalos/configurações.
* valores diretos: seleção única.

O front deve enviar apenas campos selecionados ou pré-definidos.

---

# 20. Localização

Modos principais:

```text
city
state
neighborhood
radius
related_units
```

## UX recomendada

O usuário não escolhe o `mode` diretamente.

Ele vê opções simples:

```text
Onde você procura?

Abrangência:
- Somente esta cidade
- Cidade e região próxima
- Até 50 km
- Até 100 km
- Todo o estado
```

O front traduz a escolha visual para o payload.

Exemplos:

## Somente cidade

```json
{
  "location": {
    "mode": "city",
    "city_id": 2408102
  }
}
```

## Até 50 km

```json
{
  "location": {
    "mode": "radius",
    "city_id": 2408102,
    "radius_km": 50
  }
}
```

## Todo o estado

```json
{
  "location": {
    "mode": "state",
    "state_id": 24
  }
}
```

---

# 21. Busca no PostgreSQL

No MVP, a busca deve usar PostgreSQL bem indexado.

Não usar Meilisearch/Elasticsearch/OpenSearch inicialmente, salvo necessidade real.

A API deve ter um `VehicleSearchService` ou equivalente.

Esse serviço deve permitir trocar a implementação futura da busca sem reescrever controllers e front-end.

## Estratégia inicial

* filtros por colunas indexadas;
* paginação obrigatória;
* eager loading controlado;
* busca textual simples com `ILIKE` ou recurso nativo do PostgreSQL;
* filtros por cidade, estado, loja, marca, modelo, preço, ano, câmbio, combustível, carroceria;
* raio por latitude/longitude, se necessário;
* evolução futura para PostGIS se o volume/complexidade justificar.

---

# 22. Entidades principais

Entidades principais do projeto:

* stores
* store_domains
* store_settings
* store_relationships
* users
* store_users
* vehicles
* vehicle_images
* vehicle_features
* brands
* vehicle_models
* states
* cities
* leads
* favorites
* conversations
* plans
* subscriptions
* portal_banners
* featured_placements
* audit_logs

---

# 23. Módulos da API

Módulos principais:

* Auth
* Tenancy
* Stores
* StoreSettings
* StoreDomains
* StoreRelationships
* Vehicles
* VehicleMedia
* Search
* Leads
* Favorites
* Users
* Plans
* Subscriptions
* Portal
* Analytics
* Integrations
* Sitemap
* SEO

---

# 24. Sitemap

O back-end deve possuir um módulo de geração de sitemaps.

A geração não deve acontecer diretamente no request HTTP.

Deve ser feita por jobs.

## Deve permitir execução por:

* comando Artisan;
* job em fila;
* scheduler/crontab;
* ação manual no backoffice central.

## Escopos de geração

O sistema deve permitir gerar sitemap para:

* portal central;
* todas as lojas;
* lojas selecionadas;
* somente lojas;
* somente portal central.

## Sitemap do portal central

Deve incluir:

* home;
* busca/listagens relevantes;
* páginas de veículos;
* páginas públicas das lojas;
* páginas de cidade;
* páginas de marca;
* páginas de modelo;
* páginas estratégicas de SEO;
* páginas institucionais.

## Sitemap da loja

Cada loja deve ter seu próprio sitemap considerando o domínio principal da loja.

Deve incluir:

* home da loja;
* estoque;
* veículos da loja;
* sobre;
* contato;
* unidades relacionadas, se aplicável.

## Comando recomendado

```bash
php artisan sitemap:generate --scope=all
php artisan sitemap:generate --scope=portal
php artisan sitemap:generate --scope=stores
php artisan sitemap:generate --store=15
php artisan sitemap:generate --stores=15,22,31
```

## Jobs recomendados

```text
GeneratePortalSitemapJob
GenerateStoreSitemapJob
GenerateSelectedStoresSitemapsJob
```

## Regra importante

O sitemap deve ser gerado apenas com registros públicos e ativos:

* lojas ativas;
* veículos ativos;
* anúncios publicados;
* páginas permitidas para indexação;
* domínios válidos.

---

# 25. SEO e rotas estratégicas

O projeto deve suportar rotas estratégicas de SEO.

Exemplos:

* carro por cidade;
* carro por estado;
* marca por cidade;
* modelo por cidade;
* tipo de veículo por cidade;
* faixa de preço por cidade;
* loja por cidade.

Exemplos de URLs:

```text
/veiculos/corolla/rn/natal
/veiculos/honda-civic/sp/sao-paulo
/carros-usados/toyota/rn/natal
/suvs-em-natal-rn
/carros-ate-80000-em-natal-rn
```

## Rotas sugestivas naturais

O sistema também deve suportar rotas baseadas em buscas naturais.

Exemplos:

```text
corolla em Natal
corolla automático em Natal
corolla 2012 em Natal
corolla automático 2012 em Natal
```

Exemplo de URL:

```text
/veiculos/corolla-automatico-2012-em-natal-rn
```

## Responsabilidade

O back-end deve ser responsável por:

* interpretar a URL;
* transformar intenção em filtros;
* validar relevância;
* buscar veículos;
* gerar metadados SEO;
* decidir se a rota entra no sitemap.

O front-end deve ser responsável por:

* receber a rota;
* renderizar a página;
* exibir título;
* exibir descrição;
* exibir resultados;
* consumir os filtros interpretados pela API.

## Critério obrigatório

Essas rotas não devem ser geradas indiscriminadamente.

Só devem existir se houver:

* veículos ativos;
* lojas ativas;
* combinação real;
* conteúdo útil;
* volume mínimo de anúncios;
* title e description próprios;
* permissão para indexação.

---

# 26. Diretriz visual oficial

A interface deve ser:

* moderna;
* clean;
* clara;
* comercial;
* responsiva;
* sem aparência de Bootstrap.

O tema padrão deve ser light mode.

A interface pública deve parecer um marketplace moderno, não um painel administrativo genérico.

O backoffice pode ser mais denso e funcional, mas deve manter a mesma identidade visual.

---

# 27. Botões

Os botões devem seguir estas regras:

* levemente arredondados;
* evitar `rounded-full`;
* evitar aparência Bootstrap;
* preferência por botões outlined;
* tamanhos pequenos ou médios;
* altura controlada;
* cursor pointer quando ativos;
* sem pointer quando disabled;
* estados de hover, active, focus e disabled bem definidos.

---

# 28. Inputs e formulários

Inputs, selects e textareas devem seguir estas regras:

* sem outline padrão do navegador;
* bordas suaves;
* focus discreto;
* altura compacta;
* labels pequenas;
* placeholders discretos;
* sem ring grosso;
* evitar foco azul forte padrão;
* usar `focus:ring-0` quando fizer sentido;
* se usar ring, usar no máximo `focus:ring-1` com cor discreta.

Exemplo de direção visual:

```html
<input
  class="border border-neutral-200 bg-white text-sm outline-none transition
         focus:border-neutral-300 focus:ring-0"
/>
```

---

# 29. Tailwind CSS v4

Ao usar Tailwind CSS v4:

* bordas devem ser discretas, finas e suaves;
* em alguns casos, quase invisíveis;
* evitar `ring-*` grosso em `focus` e `active`;
* preferir `focus:ring-0`;
* quando necessário, usar `focus:ring-1` com cor discreta;
* evitar aparência padrão pesada de foco;
* manter consistência visual entre portal, storefront e backoffice.

---

# 30. Tipografia

A tipografia deve ser:

* comercialmente validada;
* limpa;
* moderna;
* com boa leitura;
* com letras menores;
* com boa hierarquia visual.

Fonte preferencial:

```text
Inter
```

Alternativas aceitáveis:

```text
Manrope
DM Sans
IBM Plex Sans
```

---

# 31. Transições e animações

As transições devem ser:

* rápidas;
* suaves;
* discretas;
* com aparência de resposta imediata;
* sem mudanças bruscas.

Direção recomendada:

```text
150ms a 220ms
ease-out
```

Usar principalmente em:

* hover;
* filtros;
* drawers;
* modais;
* menus;
* cards;
* botões;
* estados interativos.

---

# 32. Componentes visuais

Os componentes devem usar:

* cards brancos;
* bordas suaves;
* sombras discretas;
* fundos cinza-claro;
* espaçamento consistente;
* ícones simples;
* layout responsivo;
* hierarquia visual clara.

---

# 33. Internacionalização

O foco principal é o mercado brasileiro.

Idioma padrão:

```text
pt-BR
```

Formatação padrão:

* moeda: BRL;
* datas no padrão brasileiro;
* textos em português do Brasil;
* números no padrão brasileiro.

Regra obrigatória:

* textos do front-end não devem ficar hardcoded nos componentes;
* usar arquivos de tradução;
* preparar a aplicação para futura internacionalização.

Estrutura sugerida:

```text
/locales
  pt-BR.json
  en-US.json
```

---

# 34. Performance

Regras principais:

* PostgreSQL bem indexado;
* Redis para cache;
* cache de resolução de domínio;
* cache de configurações da loja;
* cache de dados estáticos;
* paginação obrigatória;
* eager loading controlado;
* API Resources enxutos;
* jobs/queues para tarefas pesadas;
* upload e processamento de imagens fora do request principal quando possível;
* evitar queries pesadas sem índice;
* evitar payloads grandes desnecessários.

Caches importantes:

```text
store:domain:{domain}
store:{id}:settings
store:{id}:navigation
store:{id}:featured_vehicles
portal:home
```

---

# 35. Segurança

Regras esperadas:

* autenticação segura;
* autorização por Policies;
* validação com Form Requests;
* proteção contra acesso cruzado entre lojas;
* escopos por `store_id`;
* rate limit em endpoints sensíveis;
* sanitização de campos configuráveis;
* evitar scripts livres de lojistas;
* controle de upload de imagens;
* validação de MIME/type/tamanho;
* logs de auditoria em ações importantes;
* domínios customizados somente ativos após verificação.

---

# 36. Boas práticas Laravel

O back-end deve seguir:

* controllers enxutos;
* Form Requests para validação;
* API Resources para resposta;
* Services para regras de negócio;
* Actions quando fizer sentido;
* Policies para autorização;
* Jobs para tarefas pesadas;
* Enums para status/tipos;
* DTOs quando agregarem clareza;
* migrations bem estruturadas;
* factories usando `fake()`;
* testes para fluxos críticos;
* queries com índices adequados.

Evitar:

* controllers inchados;
* lógica duplicada;
* regras críticas no front;
* queries sem índice;
* acoplamento excessivo;
* uso de `@` para suprimir erros no PHP.

---

# 37. Boas práticas Nuxt/Vue

Os front-ends devem seguir:

* Nuxt 4;
* Vue 3 Composition API;
* componentes pequenos;
* composables por domínio;
* layouts bem definidos;
* middleware quando necessário;
* SSR quando fizer sentido;
* tipagem com TypeScript;
* validação de props;
* i18n;
* UX responsiva;
* evitar lógica de negócio crítica no front;
* evitar componentes gigantes.

---

# 38. Estrutura sugerida da API

```text
/apps/api
  /app
    /Actions
    /DTOs
    /Enums
    /Http
      /Controllers
      /Requests
      /Resources
    /Jobs
    /Models
    /Policies
    /Services
    /Scopes
  /database
    /factories
    /migrations
    /seeders
  /routes
  /tests
```

---

# 39. Estrutura sugerida dos front-ends

```text
/apps/portal
  /app
  /components
  /composables
  /layouts
  /locales
  /middleware
  /pages
  /server

/apps/storefront
  /app
  /components
  /composables
  /layouts
  /locales
  /middleware
  /pages
  /server

/apps/backoffice
  /app
  /components
  /composables
  /layouts
  /locales
  /middleware
  /pages
  /server
```

---

# 40. O que entra no MVP

## Portal central

* home;
* busca;
* listagem;
* filtros;
* detalhe do anúncio;
* página pública da loja;
* outros anúncios da loja;
* favoritos;
* login/cadastro;
* contato/leads;
* CTA "Ver no site da loja";
* SEO básico.

## Storefront

* resolução por domínio;
* home da loja;
* estoque;
* detalhe do veículo;
* sobre;
* contato;
* unidades relacionadas;
* tema/configuração da loja;
* SEO da loja.

## Backoffice

* login;
* administração central;
* administração da loja;
* cadastro de loja;
* cadastro de veículo;
* upload de imagens;
* configuração da loja;
* gestão de usuários;
* gestão de leads;
* gestão de domínios;
* gestão de sitemap/SEO;
* gestão de relacionamentos entre lojas.

## API

* autenticação;
* tenant resolution;
* CRUD de lojas;
* CRUD de domínios;
* CRUD de configurações;
* CRUD de veículos;
* upload de imagens;
* leads;
* favoritos;
* busca;
* sitemap;
* SEO;
* permissões;
* auditoria básica.

---

# 41. O que fica para depois

* Meilisearch/Elasticsearch/OpenSearch;
* PostGIS, se não for necessário no início;
* múltiplos bancos por loja;
* schema separado por loja;
* deploy por loja;
* microserviços;
* regras avançadas de ranking;
* integração completa com portais externos;
* social login;
* comparação avançada de veículos;
* recomendação inteligente;
* automações comerciais avançadas.

---

# 42. Ambiente local esperado

O ambiente local pode usar:

* PostgreSQL local;
* Redis local;
* mkcert para SSL local;
* Nginx local com múltiplos domínios.

Exemplo de domínios técnicos:

```env
LOCAL_API_DOMAIN=api.rederevenda.com
LOCAL_PORTAL_DOMAIN=rederevenda.com
LOCAL_BACKOFFICE_DOMAIN=admin.rederevenda.com
LOCAL_STOREFRONT_BASE_DOMAIN=site.rederevenda.com
```

Exemplo de portas locais:

```env
PORTAL_PORT=7030
API_PORT=7031
BACKOFFICE_PORT=7032
STOREFRONT_PORT=7033
```

Mapeamento esperado:

```text
https://rederevenda.com -> portal central
https://api.rederevenda.com -> API Laravel
https://admin.rederevenda.com -> backoffice
https://site.rederevenda.com -> domínio técnico/base do storefront
https://*.site.rederevenda.com -> lojas internas
```

Domínios customizados locais podem ser adicionados manualmente no Nginx para teste.

---

# 43. Definição de pronto

O projeto só pode ser considerado pronto para primeira versão quando os itens abaixo estiverem funcionando.

## Infraestrutura

* monorepo estruturado;
* Laravel API rodando;
* Nuxt portal rodando;
* Nuxt storefront rodando;
* Nuxt backoffice rodando;
* PostgreSQL configurado;
* Redis configurado;
* migrations executando;
* seeders básicos funcionando;
* filas/jobs funcionando;
* storage configurado.

## Domínios e tenant

* domínio do portal funcionando;
* domínio da API funcionando;
* domínio do backoffice funcionando;
* domínio base do storefront funcionando;
* domínio interno da loja funcionando;
* resolução de loja por host funcionando;
* cache de domínio funcionando;
* loja não configurada retornando erro adequado;
* domínio customizado preparado para verificação.

## Portal central

* home pública funcionando;
* busca global funcionando;
* filtros funcionando;
* paginação funcionando;
* detalhe do veículo funcionando;
* página da loja dentro do portal funcionando;
* outros veículos da loja funcionando;
* CTA "Ver no site da loja" funcionando;
* lead/contato funcionando;
* favoritos funcionando;
* login/cadastro do visitante funcionando;
* SEO básico funcionando.

## Storefront

* uma única aplicação atendendo múltiplas lojas;
* loja resolvida pelo domínio;
* configurações carregadas corretamente;
* tema/layout aplicado;
* home da loja funcionando;
* estoque funcionando;
* detalhe do veículo funcionando;
* contato funcionando;
* sobre funcionando;
* unidades relacionadas funcionando quando existirem;
* SEO da loja funcionando.

## Backoffice

* login funcionando;
* permissões funcionando;
* visão de admin central funcionando;
* visão de admin da loja funcionando;
* criação de loja funcionando;
* geração de domínio interno funcionando;
* cadastro de veículo funcionando;
* edição de veículo funcionando;
* upload de imagens funcionando;
* gestão de leads funcionando;
* gestão de usuários funcionando;
* gestão de configurações da loja funcionando;
* gestão de domínios funcionando;
* disparo de sitemap funcionando.

## SEO e sitemap

* sitemap do portal gerado por job;
* sitemap das lojas gerado por job;
* geração via command Artisan;
* geração via scheduler preparada;
* rotas estratégicas funcionando;
* rotas sugestivas funcionando quando relevantes;
* páginas vazias não sendo indexadas;
* title e description gerados corretamente.

## UI/UX

* visual moderno e clean;
* light mode como padrão;
* botões discretos e consistentes;
* inputs sem outline pesado;
* focus sutil;
* bordas finas;
* tipografia padronizada;
* responsividade funcionando;
* textos via i18n;
* `pt-BR` como padrão.

---

# 44. Critério final de aceite

O projeto será aceito como primeira versão quando for possível:

1. criar uma loja pelo backoffice central;
2. gerar automaticamente o domínio interno da loja;
3. acessar o site da loja pelo domínio interno;
4. cadastrar um veículo pela loja;
5. enviar imagens do veículo;
6. visualizar o veículo no site da loja;
7. visualizar o veículo no portal central;
8. abrir o detalhe completo do veículo dentro do portal;
9. ver outros veículos da mesma loja dentro do portal;
10. clicar em "Ver no site da loja";
11. gerar lead/contato;
12. favoritar veículo como visitante logado;
13. gerar sitemap do portal;
14. gerar sitemap da loja;
15. acessar rotas SEO relevantes;
16. manter isolamento correto entre lojas;
17. manter o sistema organizado, rápido, seguro e pronto para evoluir.

---

# 45. Diretriz final

A primeira versão deve entregar uma base real de produto, não apenas uma prova de conceito.

O sistema deve nascer simples, mas profissional.

A prioridade é:

```text
funcionar bem
ser seguro
ser rápido
ser organizado
ser fácil de evoluir
não ter excesso de engenharia
```

O MVP deve permitir que uma loja seja criada, tenha seu site publicado, cadastre veículos e apareça no portal central com busca, SEO, contato e estrutura mínima de operação.
