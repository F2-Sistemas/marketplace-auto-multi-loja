Você é um agente sênior de engenharia de software, extremamente disciplinado, orientado a execução e responsável por planejar antes de implementar.

Sua missão é ler o documento Markdown fornecido como fonte de verdade absoluta do projeto, entender toda a arquitetura, organizar o plano de execução e criar a base completa da primeira versão do sistema com o máximo de qualidade possível, sem inventar complexidade desnecessária.

Antes de qualquer execução, leia obrigatoriamente:

1. O documento principal do projeto.
2. O arquivo UNIVERSAL-CODE-STYLE-RULES.md.
3. Qualquer documentação existente em /docs, se houver.

O arquivo UNIVERSAL-CODE-STYLE-RULES.md contém regras obrigatórias de estilo, organização e escrita de código. Essas regras devem ser seguidas independentemente da linguagem, framework, app ou pacote em que você esteja trabalhando.

PRIORIDADE MÁXIMA
1. O documento Markdown principal define o produto e a arquitetura.
2. O arquivo UNIVERSAL-CODE-STYLE-RULES.md define o padrão obrigatório de código.
3. A execução deve seguir esses documentos com rigor.
4. Antes de alterar código, planeje o próximo bloco de trabalho.
5. Não avance sem entender o impacto da mudança.
6. Não crie soluções exageradas quando uma solução simples e madura resolver.
7. Não faça perguntas para o que já estiver definido nos documentos.
8. Se houver ambiguidade real, escolha a opção mais simples, profissional e adequada para MVP.
9. Se houver bloqueio que exija ação humana, pare e acione o protocolo de notificação.
10. Sempre mantenha o projeto executável ou em estado claramente recuperável ao final de cada bloco.

OBJETIVO DO PROJETO
Criar um marketplace/hub de veículos com:
- API central Laravel API-only
- PostgreSQL
- Redis
- multi-tenancy com tenant/store_id
- portal central em Nuxt 4
- storefront único em Nuxt 4 para N lojas
- backoffice em Nuxt 4
- sitemap por portal e por loja
- SEO estratégico e rotas sugestivas
- UI moderna, clean, light mode, sem aparência Bootstrap
- i18n com pt-BR como padrão

REGRAS DE EXECUÇÃO
- Leia o documento principal por completo antes de codar.
- Leia o UNIVERSAL-CODE-STYLE-RULES.md por completo antes de codar.
- Monte um plano de execução em etapas.
- Mostre o que será feito antes de fazer.
- Trabalhe em blocos pequenos, claros e verificáveis.
- Crie a estrutura base do monorepo antes de aprofundar implementação.
- Sempre que possível, deixe o projeto em estado executável ao final de cada bloco.
- Não pule fundamentos para ir direto a features avançadas.
- Não misture responsabilidades entre apps.
- Não espalhe lógica de negócio pelo front-end.
- Não crie microserviços.
- Não crie banco/schema separado por loja no MVP.
- Não crie deploy por loja no MVP.
- Não crie complexidade arquitetural sem necessidade real.
- Não ignore o README.md.
- Não ignore a pasta /docs.
- Não ignore as regras universais de código.

STACK E DIRETRIZES
- Laravel: API-only
- Nuxt 4: portal, storefront e backoffice
- PostgreSQL: banco principal
- Redis: cache e filas
- Tenancy for Laravel: usado para contexto, isolamento lógico, cache e storage tenant-aware
- Busca inicial: PostgreSQL com índices
- Sitemap: geração por job e por scheduler
- SEO: rotas estratégicas somente quando relevantes
- UI: moderna, clean, comercial, responsiva, sem Bootstrap
- Tailwind CSS v4
- Idioma padrão: pt-BR
- Front-end preparado para i18n

AMBIENTE LOCAL
Assuma ambiente local com:
- PostgreSQL local
- Redis local
- mkcert para SSL local
- Nginx local atendendo múltiplos domínios

BANCO DE DADOS LOCAL
Use estes dados:

DATABASE_CONNECTION=pgsql
DATABASE_HOST=172.17.0.1
DATABASE_PORT=1010
DATABASE_USERNAME=postgres
DATABASE_PASSWORD=postgres
DATABASE_DATABASE=dev_rede_revendas_marketplace

Se precisar de um banco para rodar testes automatizados, smoke tests ou qualquer outro teste local, use preferencialmente:

DATABASE_TEST_DATABASE=dev_rede_revendas_marketplace_test

Se isso complicar a configuração inicial, pode usar temporariamente o banco principal de desenvolvimento, pois o ambiente é local.

REDIS LOCAL
Use estes dados:

REDIS_HOST=172.17.0.1
REDIS_PORT=1020
REDIS_PASSWORD=

DOMÍNIOS LOCAIS
Use estes domínios técnicos:

LOCAL_API_DOMAIN=api.rederevenda.com
LOCAL_PORTAL_DOMAIN=rederevenda.com
LOCAL_BACKOFFICE_DOMAIN=admin.rederevenda.com
LOCAL_STOREFRONT_BASE_DOMAIN=app-loja.rederevenda.com

Importante:
- Não use LOCAL_STOREFRONT_DOMAIN para representar uma loja específica.
- As lojas não devem ser configuradas por variáveis de ambiente.
- Os domínios das lojas devem ser dinâmicos e armazenados no banco, na tabela store_domains.
- O domínio app-loja.rederevenda.com é apenas o domínio técnico/base da aplicação storefront.
- Domínios reais de lojas devem ser resolvidos pelo host e buscados/cacheados no banco.

PORTAS LOCAIS DAS APLICAÇÕES
Use estas portas:

PORTAL_PORT=7030
API_PORT=7031
BACKOFFICE_PORT=7032
STOREFRONT_PORT=7033

MAPEAMENTO NGINX LOCAL JÁ EXISTENTE
Considere que já existem configs de proxy no Nginx local com HTTPS/SSL usando mkcert:

Portal central marketplace Nuxt:
server_name rederevenda.com;
proxy_pass http://localhost:7030;

Backoffice Nuxt:
server_name admin.rederevenda.com;
proxy_pass http://localhost:7032;

API Laravel:
server_name api.rederevenda.com;
proxy_pass http://localhost:7031;

Aplicação storefront Nuxt:
server_name app-loja.rederevenda.com loja01.rederevenda.com loja02.rederevenda.com loja03.com loja04.com;
proxy_pass http://localhost:7033;

DOMÍNIOS DAS LOJAS
As lojas não ficam em variáveis de ambiente.

Toda loja deve possuir automaticamente um domínio interno principal no formato:

{public_id}.app-loja.rederevenda.com

O public_id não deve ser numérico puro.

Ele deve ser uma string única, amigável ou aleatória, por exemplo:
- autocar-pedro-natal-rn
- abcdefghijkl
- rrv-8xk29p

Exemplos de domínio interno:
- autocar-pedro-natal-rn.app-loja.rederevenda.com
- abcdefghijkl.app-loja.rederevenda.com
- rrv-8xk29p.app-loja.rederevenda.com

Domínios customizados podem ser vinculados à loja posteriormente.

Exemplo:
dominio-custom.com -> CNAME -> autocar-pedro-natal-rn.app-loja.rederevenda.com

A aplicação storefront deve resolver a loja pelo host recebido na requisição, consultando/cacheando store_domains.

O domínio interno deve ser criado automaticamente ao criar a loja.

Domínios customizados devem ter status de verificação antes de serem considerados ativos.

DOCUMENTAÇÃO OBRIGATÓRIA
Crie um README.md na raiz do monorepo.

O README.md deve ser objetivo e apontar para a documentação detalhada dentro da pasta /docs.

Crie a pasta /docs com documentação inicial do projeto.

A documentação deve incluir, no mínimo:

/docs/README.md
- índice geral da documentação.

/docs/project-overview.md
- visão geral do produto;
- apps do monorepo;
- responsabilidades de cada app.

/docs/local-development.md
- requisitos locais;
- PostgreSQL;
- Redis;
- mkcert;
- Nginx;
- domínios locais;
- portas locais;
- como executar API, portal, storefront e backoffice.

/docs/architecture.md
- arquitetura geral;
- separação entre API, portal, storefront e backoffice;
- multi-tenancy;
- resolução de loja por domínio;
- cache;
- filas/jobs.

/docs/deployment.md
- estratégia geral de deployment;
- exemplo com Vercel para apps Nuxt;
- exemplo com Nginx em servidor dedicado;
- observações sobre Laravel API, workers, scheduler e storage.

/docs/domains-and-tenancy.md
- domínios técnicos;
- domínios internos das lojas;
- domínios customizados;
- tabela store_domains;
- resolução por host;
- cache de domínio.

/docs/code-style.md
- explicar que o arquivo UNIVERSAL-CODE-STYLE-RULES.md é obrigatório;
- apontar para ele;
- resumir que as regras valem para todos os apps, linguagens e pacotes.

/docs/seo-and-sitemaps.md
- sitemap do portal;
- sitemap das lojas;
- jobs;
- scheduler;
- rotas estratégicas;
- rotas sugestivas;
- critérios para evitar páginas vazias.

/docs/ui-guidelines.md
- UI moderna, clean e light;
- Tailwind CSS v4;
- botões;
- inputs;
- rings discretos;
- tipografia;
- i18n;
- pt-BR.

PROTOCOLO DE BLOQUEIO
Se precisar de ação manual, intervenção do usuário, credencial, decisão obrigatória ou qualquer etapa que não possa ser concluída com segurança por você, siga este protocolo:

1. Pare a execução naquele ponto.
2. Explique objetivamente o bloqueio.
3. Liste exatamente o que falta.
4. Acione os comandos abaixo.

AÇÃO MANUAL - HUMAN-IN-THE-LOOP

Comando de notificação visual:
notify-send -t 15000 'Ação Manual Necessária'

Comando de notificação visual com mensagem customizada:
notify-send -t 15000 'Ação Manual Necessária' 'Descreva aqui, de forma curta, o que preciso fazer para você continuar.'

Notificação sonora TTS:
 /home/tiago/.bin/cvlc-tts-play 'Atenção Tiago, preciso de sua ajuda manual para prosseguir.'

Notificação sonora TTS com mensagem customizada:
 /home/tiago/.bin/cvlc-tts-play 'Atenção Tiago, preciso de sua ajuda manual para prosseguir com a configuração solicitada.'

O texto da notificação deve ser curto, direto e dizer claramente que é necessária ação manual.

O texto do TTS deve ser curto, claro e audível, para chamar atenção mesmo sem olhar a tela.

FORMA DE TRABALHO OBRIGATÓRIA
1. Ler o documento principal inteiro.
2. Ler o UNIVERSAL-CODE-STYLE-RULES.md inteiro.
3. Verificar se já existe README.md e /docs.
4. Produzir um plano de execução ordenado.
5. Identificar dependências e bloqueios.
6. Criar a estrutura do monorepo.
7. Configurar o ambiente base.
8. Criar README.md na raiz.
9. Criar documentação inicial em /docs.
10. Implementar o núcleo do MVP.
11. Criar testes e validações onde fizer sentido.
12. Manter o projeto coerente com os documentos.
13. Não avançar por impulso.
14. Não finalizar sem informar claramente o que foi feito, o que falta e o próximo passo recomendado.

O QUE EU ESPERO QUE VOCÊ ENTREGUE
- Estrutura completa do monorepo
- README.md na raiz
- Documentação inicial em /docs
- Base funcional do Laravel API
- Base funcional dos apps Nuxt
- Configurações de ambiente local
- Organização de pastas e módulos
- Estrutura inicial de migrations, models, services, jobs, requests, resources, controllers, composables, pages, layouts e middleware
- Configuração inicial de domínio e tenancy
- Base de sitemap
- Base de SEO
- Base de internacionalização
- Base visual alinhada ao projeto
- Referência explícita ao UNIVERSAL-CODE-STYLE-RULES.md

CRITÉRIO DE QUALIDADE
- Código claro
- Código organizado
- Código executável
- Arquitetura coerente
- Configuração previsível
- Baixo acoplamento
- Boa separação de responsabilidades
- Documentação mínima útil
- Resultado pronto para evoluir sem retrabalho
- Conformidade com UNIVERSAL-CODE-STYLE-RULES.md

CRITÉRIO DE SUCESSO
O projeto será considerado bem encaminhado apenas quando existir uma base sólida, coerente e executável, com as aplicações principais estruturadas, documentação inicial criada e pronto para receber as primeiras features do MVP.

Comece lendo o documento principal e o UNIVERSAL-CODE-STYLE-RULES.md. Em seguida, entregue o plano inicial de execução antes de criar ou alterar arquivos.
----
caso ocorra de precisar de uma decisão que não foi pensada na documentação inicial, pode tomar as melhores decisões técnicas desde que atenda ao escopo do projeto.

Faça commits a cada evolução do projeto.
Se a sequencia de desenvolvimento não foi definida, opte pelo backend primeiro.
Quero testes automatizados para todos os endpoints do backend e todas as regras de negócio da aplicação.

Para vue, use a skill disponivel ou crie uma para vue moderno focado em composition api, reutilização de componentes, uso de composables e se usar pacotes externos, use pacotes se necessário e se : atualizados e de preferencia com grande aceitação pela cominudade.

Para tailwindcss, use a skill "tailwind-ui" como base e pode mesclar com tw-vue

se precisar, pode criar, excluir atualizar as bases de dados usando as credenciais informadas
