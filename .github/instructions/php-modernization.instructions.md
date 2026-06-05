---
description: "Use when editing legacy PHP files for modernization, migration to PHP 8.5.3, deprecation review, syntax updates, and safe incremental refactoring."
applyTo: "**/*.php"
---

# PHP Modernization

Ao editar arquivos PHP deste projeto:

- verificar compatibilidade com PHP 8.5.3
- identificar funções removidas ou obsoletas
- sugerir melhorias de tipagem quando seguras
- priorizar refatoração incremental
- aplicar melhorias sem alterar regra de negócio
- preferir código legível e previsível

## Diretrizes
- não introduzir framework
- evitar reescrita total
- manter impacto controlado
- sinalizar riscos antes de mudanças semânticas

## Skill padrão
- preferir `php-legacy-modernizer` para casos genéricos
- usar `workcontrol-legacy-fixer` apenas quando houver convenções internas específicas do WorkControl

## Fluxo operacional mínimo
1. `bash .github/skills/php-legacy-modernizer/scripts/fix_php_legacy_generic.sh --dry-run <diretorio>`
2. revisar diff e riscos
3. `bash .github/skills/php-legacy-modernizer/scripts/fix_php_legacy_generic.sh --apply <diretorio>`
4. validar lint e consolidar pendências de revisão manual
