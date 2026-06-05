# Manual Prático — php-legacy-modernizer

## Objetivo

Padronizar o uso da skill `php-legacy-modernizer` para modernização incremental e segura de PHP legado genérico.

## Quando usar

- módulos PHP legados sem convenções internas do WorkControl
- necessidade de conversão mecânica de sintaxe alternativa
- necessidade de validação em lote com `php -l`

## Fluxo recomendado

1. Dry-run:
`bash .github/skills/php-legacy-modernizer/scripts/fix_php_legacy_generic.sh --dry-run <diretorio>`
2. Revisar diff e riscos
3. Apply:
`bash .github/skills/php-legacy-modernizer/scripts/fix_php_legacy_generic.sh --apply <diretorio>`
4. Consolidar resultado do lint
5. Reportar alterações e pendências de revisão manual

## Quando não usar

- módulo com regras internas específicas do WorkControl
- necessidade de migração de helper legado específico de domínio

Nesses casos, usar `workcontrol-legacy-fixer`.
