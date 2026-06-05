---
description: "Use when editing WorkControl legacy modules that rely on project-specific helpers, CRUD object conventions, legacy helper calls, autoload migration, and internal normalization rules."
applyTo: "**/*.php"
---

# WorkControl Conventions

Este projeto possui convenções internas específicas.

## Convenções importantes

- chamadas legadas `Erro(...)` e `ajaxErro(...)` devem ser substituídas por `Check::ajaxErro(...)` quando compatível
- helpers legados devem migrar para `App\Helpers\Check`
- objetos CRUD devem seguir o padrão:
  - `$Read`
  - `$Create`
  - `$Update`
  - `$Delete`
  - `$Upload`
- quando necessário, inicializar objetos CRUD com `??=`
- preferir `require __DIR__ . '/../../vendor/autoload.php';` no lugar de includes legados de bootstrap, quando a estrutura do módulo for compatível

## Regras
- validar semântica antes de substituir chamadas globais por métodos estáticos
- conferir imports antes de concluir a alteração
- evitar mudanças cegas em arquivos altamente dinâmicos

