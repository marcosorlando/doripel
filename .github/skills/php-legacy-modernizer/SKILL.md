---
name: php-legacy-modernizer
description: "Use when modernizing legacy PHP in any module with safe incremental refactors, converting alternative syntax to braces, and validating with php -l."
---

# php-legacy-modernizer

Use esta skill para modernizar arquivos PHP legados de forma incremental, previsivel e segura.

## Quando usar

- modulo legado sem especificidades do WorkControl
- necessidade de conversao mecanica de `if/elseif/else`, `foreach`, `for`, `while` e `switch` em sintaxe alternativa
- necessidade de validacao em lote com `php -l`

## Script principal

- `scripts/fix_php_legacy_generic.sh`

## Workflow

1. Executar em simulacao:
   - `bash .github/skills/php-legacy-modernizer/scripts/fix_php_legacy_generic.sh --dry-run <diretorio>`
2. Revisar diff e pontos de risco.
3. Aplicar mudancas:
   - `bash .github/skills/php-legacy-modernizer/scripts/fix_php_legacy_generic.sh --apply <diretorio>`
4. Revisar resultado do lint.

## Uso

```bash
bash .github/skills/php-legacy-modernizer/scripts/fix_php_legacy_generic.sh --dry-run <diretorio>
bash .github/skills/php-legacy-modernizer/scripts/fix_php_legacy_generic.sh --apply <diretorio>
bash .github/skills/php-legacy-modernizer/scripts/fix_php_legacy_generic.sh --apply <diretorio> --report reports/php-modernizer.txt
```

## Limites

- nao altera regra de negocio
- nao substitui revisao manual em arquivos dinamicos
- para regras especificas do projeto WorkControl, usar `workcontrol-legacy-fixer`
