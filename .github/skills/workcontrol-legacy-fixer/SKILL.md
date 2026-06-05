---
name: workcontrol-legacy-fixer
description: "Use when fixing WorkControl legacy PHP modules, replacing project-specific legacy helper calls, normalizing CRUD object conventions, converting alternative control syntax to braces, migrating bootstrap includes to Composer autoload, adding missing imports, initializing CRUD objects with ??=, and batch-validating PHP files with php -l on PHP 8.5.3."
---

# WorkControl Legacy Fixer

## Overview

Aplica normalização mecânica e segura em módulos legados do WorkControl, focando em convenções internas do projeto,
aderência a PSR, compatibilidade com PHP 8.5.3 e redução de erros por chamadas legadas, imports ausentes e inicialização
inconsistente de objetos CRUD.

## When To Use

Use esta skill quando:

- houver módulos WorkControl com helpers legados
- houver chamadas antigas como `Erro(...)` ou `ajaxErro(...)`
- houver uso inconsistente de objetos CRUD
- houver includes legados de bootstrap
- houver necessidade de lint em lote
- houver divergência entre estilo legado e padrão PSR esperado no projeto

Para casos genéricos sem convenções internas do WorkControl, preferir `php-legacy-modernizer`.

## Workflow

1. Definir diretório-alvo.
2. Executar primeiro em modo seguro:
    - `scripts/fix_php_legacy.sh --dry-run <diretorio>`
3. Revisar diff e warnings.
4. Se estiver seguro, aplicar:
    - `scripts/fix_php_legacy.sh --apply <diretorio>`
5. Rodar revisão manual nos arquivos críticos ou dinâmicos.
6. Consolidar ajustes restantes.
7. Resumir alterações, riscos e pendências.

## What The Script Changes

- Renomear métodos legados para camelCase:
    - `ExeRead` / `FullRead` / `ExeCreate` / `ExeUpdate` / `ExeDelete`
    - `ExePager` / `ExePaginator` / `ReturnPage`
- Normalizar aliases legados em chamadas helper/model para camelCase:
    - `Check::Name`
    - `Check::CPF`
    - `Check::CNPJ`
    - `Check::Nascimento`
    - `Upload->Image`
- Substituir funções globais legadas por `Check::...` quando houver equivalente:
    - `getWcLevel(...)` -> `Check::getWcLevel(...)`
    - `getCapilalize(...)` -> `Check::getCapilalize(...)`
    - `dadedo(...)` -> `Check::getCapilalize(...)`
- Converter sintaxe alternativa de controle para chaves:
    - `if (...) : ... endif;`
    - `foreach (...) : ... endforeach;`
    - `for (...) : ... endfor;`
    - `while (...) : ... endwhile;`
    - `switch (...) : ... endswitch;`
    - `else:`
    - `elseif (...):`
- Renomear:
    - `Erro(...)` -> `Check::Erro(...)`
    - `AjaxErro(...)` -> `Check::ajaxErro(...)`
    - `ajaxErro(...)` -> `Check::ajaxErro(...)`
- Inserir `use` faltantes quando necessário:
    - `App\Conn\Read`
    - `App\Conn\Create`
    - `App\Conn\Update`
    - `App\Conn\Delete`
    - `App\Models\Upload`
    - `App\Models\Pager`
    - `App\Helpers\Check`
- Forçar objetos CRUD com inicial maiúscula:
    - `$Read`
    - `$Delete`
    - `$Update`
    - `$Upload`
    - `$Create`
- Quando variável CRUD existir no arquivo e não tiver inicialização, inserir:
    - `$Read ??= new Read();`
    - `$Delete ??= new Delete();`
    - `$Update ??= new Update();`
    - `$Upload ??= new Upload();`
    - `$Create ??= new Create();`
- Normalizar paginação antes de `exePager()`:
    - detectar `filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT)`
    - aplicar fallback inteiro seguro:
        - `$page = (is_int($page) && $page > 0) ? $page : 1;`
- Quando houver warning `htmlspecialchars(): Passing null...`:
    - substituir `htmlspecialchars($value)` por `Check::safeHtmlChars($value)` em casos simples
    - substituir `array_map('htmlspecialchars', $arr)` por callback seguro
- Quando houver erro do tipo `Call to undefined function`:
    - procurar método equivalente em `_app/Helpers/Check.php`
    - substituir chamada por `Check::nomeDoMetodo(...)`
    - garantir `use App\Helpers\Check;`
- Sempre que encontrar:
    - `require '../../_app/Config.inc.php';`
      substituir por:
    - `require __DIR__ . '/../../vendor/autoload.php';`
- Sempre que encontrar 'curl_close' exlcuir linha inteira:
    - Function curl_close() is deprecated since 8.5
- Rodar `php -l` em todos os `.php` do diretório-alvo.

## Safety Rules

- Executar somente no diretório solicitado.
- Não usar comandos destrutivos.
- Se o lint falhar, reportar arquivos e linhas.
- Em arquivos muito dinâmicos, validar semântica antes de concluir.
- Não alterar regra de negócio.

## References

Consultar `references/patterns.md` para regras internas e limites da transformação.

## Output Expected

Ao final, entregar:

- arquivos alterados
- warnings e erros de lint
- pontos com revisão manual obrigatória
- próximos passos recomendados
