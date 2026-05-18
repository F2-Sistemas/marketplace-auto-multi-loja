# Diretrizes de UI/UX e Visual - AutoHub Central

A interface visual do **AutoHub Central** é desenhada para parecer um produto comercial premium, moderno, rápido e focado em conversão. Este documento apresenta as regras de design que devem ser rigorosamente aplicadas no portal, storefronts e backoffice usando o **Tailwind CSS v4**.

---

## Filosofia de Design e Estética Premium

* **Tema Padrão**: Light Mode como padrão de produção.
* **Aparência Comercial Moderna**: Evite elementos pesados que remetam a sistemas corporativos antigos ou ao visual cru do Bootstrap (como cantos excessivamente retos, sombras pretas marcadas e botões gigantes cheios de cor primária).
* **Paleta de Cores Curada**: Cores neutras frias suaves, com acentos de destaque sutis e elegantes (ex: tons de cinza discretos `neutral-50` a `neutral-900`, e realces em tons harmoniosos como azul escuro ardósia ou verde esmeralda suave para contatos).
* **Consistência de Bordas e Cantos**: Todos os cartões, inputs e contêineres interativos utilizam cantos levemente arredondados (`rounded-lg` ou `rounded-xl`). Evite o uso de cantos completamente redondos (`rounded-full`), exceto para avatares ou ícones em círculo perfeito.

---

## Tipografia Padrão

A tipografia deve garantir alta legibilidade, espaçamento equilibrado e elegância.
* **Fonte Oficial**: **Inter** (carregada via Google Fonts).
* **Hierarquia Visual**:
  - Títulos principais (`h1`, `h2`): Semibold/Bold com peso controlado (`tracking-tight`).
  - Textos de corpo e dados técnicos: Tamanho ligeiramente reduzido (`text-sm` ou `text-xs` para metadados de estoque), garantindo que mais informações caibam na tela de forma limpa e harmônica.

---

## Componentes de Formulário e Inputs Discretos

Os campos de entrada de dados devem ser elegantes, sem contornos exagerados que poluam a tela.

### Diretrizes de Inputs:
* **Cantos e Bordas**: Suaves (`border-neutral-200`).
* **Estados de Foco (Discretos)**: Nunca utilize a borda azul grossa padrão do navegador ou anéis (`rings`) espessos e brilhantes.
* **Fórmula de Foco Recomendada**: Desative o anel grosso (`focus:ring-0`) e promova uma transição suave alterando a cor da borda para um cinza ligeiramente mais escuro (`focus:border-neutral-400`).

```vue
<!-- Exemplo de Input Elegante no Nuxt/Vue -->
<template>
  <input
    type="text"
    placeholder="Ex: Toyota Corolla"
    class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-800 outline-none transition duration-150 ease-out placeholder:text-neutral-400 focus:border-neutral-400 focus:ring-0"
  />
</template>
```

---

## Botões e Estados Interativos

Os botões devem ter comportamento claro de clique e hover rápido, sem demoras ou animações pesadas.

### Diretrizes de Botões:
* **outlined como Preferência**: Utilize botões com borda fina e fundo transparente como padrão secundário, deixando a cor preenchida apenas para a ação de conversão principal (ex: "Enviar Proposta" ou "Falar no WhatsApp").
* **Transições Suaves**: Toda mudança de estado (hover, active, focus) deve usar transição rápida de 150ms a 220ms com curva `ease-out`.
* **Estados de Cursor**: `cursor-pointer` quando interativo e `cursor-not-allowed` + opacidade reduzida quando desativado (`disabled`).

```vue
<!-- Exemplo de Botão de Conversão Premium -->
<template>
  <button
    class="inline-flex items-center justify-center rounded-lg border border-emerald-600 bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition duration-150 ease-out hover:bg-emerald-700 hover:border-emerald-700 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
  >
    <iconify-icon icon="mdi:whatsapp" class="mr-2 h-4 w-4"></iconify-icon>
    Chamar no WhatsApp
  </button>
</template>
```

---

## Internacionalização (i18n) Obrigatória

O foco inicial de mercado é o Brasil, mas a aplicação está 100% pronta para escala multi-idioma:
* **Idioma Padrão**: `pt-BR`
* **Localização Padrão**: Formatação de moeda em BRL (R$), datas no formato `dd/mm/aaaa` e números com separador de milhar por ponto e decimal por vírgula.
* **Sem Hardcoding**: Nenhuma string de texto estático pode ser colocada diretamente no HTML ou Vue templates. Todas devem ser chamadas via chaves de tradução:

```vue
<!-- Padrão Correto de Tradução no Front-end -->
<template>
  <div>
    <h2>{{ $t('portal.home.title') }}</h2>
    <p>{{ $t('portal.home.subtitle') }}</p>
  </div>
</template>
```
* **Arquivos de Tradução**: Salvos em `/locales/pt-BR.json` e `/locales/en-US.json` em cada aplicativo Nuxt.
