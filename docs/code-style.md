# Estilo de Código e Diretrizes de Escrita - AutoHub Central

A consistência de código, legibilidade e segurança são pilares fundamentais no ecossistema do **AutoHub Central**. Este documento orienta desenvolvedores humanos e assistentes de IA sobre o estilo de codificação obrigatório, apontando para o arquivo central de regras do repositório.

---

## O Arquivo Central de Estilo de Código

Todas as linguagens, frameworks, aplicações e pacotes contidos neste monorepo devem obrigatoriamente seguir as diretrizes explícitas definidas no documento raiz do projeto:

📄 **[UNIVERSAL-CODE-STYLE-RULES.md](../UNIVERSAL-CODE-STYLE-RULES.md)**

Nenhum desenvolvedor ou assistente de IA deve violar estas regras sob qualquer circunstância. A clareza e legibilidade do código sempre têm prioridade sobre a brevidade ou "truques" de sintaxe complexos.

---

## Resumo dos Princípios Não-Negociáveis

Abaixo estão os conceitos centrais que orientam a codificação no ecossistema:

### 1. Retorno Precoce (Early Return / Guard Clauses)
Evite profundamente estruturas aninhadas (`nested blocks`). Valide as entradas de dados e condições de erro primeiro, abortando a execução imediatamente. O fluxo ideal de sucesso ("happy path") deve ser linear e legível no nível mais externo da função.

### 2. Padrão "Sem Else" (Else-less Pattern)
O uso de cláusulas `else` ou `elseif` deve ser evitado ao máximo. Se uma condição `if` resulta em um retorno de valor ou disparo de exceção, as linhas subsequentes não necessitam de um bloco `else`.

```php
// ❌ Incorreto
if ($user === null) {
    return 'Guest';
} else {
    return $user->name;
}

//  Correto
if ($user === null) {
    return 'Guest';
}

return $user->name;
```

### 3. Uso Obrigatório de Chaves (Braces)
Nunca use estruturas de fluxo de controle em linha única sem chaves de escopo. Todos os blocos `if`, `for`, `foreach`, `while`, `try`, `catch` devem declarar suas chaves explicitamente em linhas separadas.

```php
// ❌ Incorreto
if ($isAdmin) return true;

//  Correto
if ($isAdmin) {
    return true;
}
```

### 4. Sem Comportamento Implícito
Não assuma ou confie em tipos dinâmicos implícitos. Escreva código fortemente tipado.
* **PHP**: Toda função e método deve declarar obrigatoriamente a tipagem rígida de tipos (`declare(strict_types=1);`), tipagem dos parâmetros de entrada e o tipo de retorno explicitamente.
* **TypeScript**: Evite o uso de `any`. Declare interfaces claras nos pacotes `@packages/types` e utilize tipos explícitos em todas as declarações de propriedades, props e composables no Vue/Nuxt.

### 5. Formatação Vertical e Legibilidade
* Separe blocos lógicos dentro de métodos e funções com uma única linha em branco (ex: separe a validação inicial do preparo de dados, a execução da lógica e o retorno).
* Limite o tamanho de arquivos e funções de acordo com a responsabilidade única (Single Responsibility Principle).

---

## Ferramentas de Linting e Validação

O projeto possui validação estrita automatizada configurada na raiz de suas aplicações.
* **Back-end (Laravel)**: Executa o **Laravel Pint** configurado sob a regra padrão PSR-12, alinhado com as regras do `UNIVERSAL-CODE-STYLE-RULES.md`. Para formatar e inspecionar o código, execute:
  ```bash
  cd apps/api
  ./vendor/bin/pint
  ```
* **Front-end (Nuxt/Vue)**: Utiliza **ESLint** com regras estritas de formatação de tags, importações e tipos.
