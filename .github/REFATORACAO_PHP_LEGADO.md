# Plano de Refatoração PHP Legado

## Objetivo
Modernizar código PHP legado com baixo risco, padronizando sintaxe, estilo e validação incremental.

## Estratégia por ondas
1. Onda 1 (baixo risco)
- Converter sintaxe alternativa de controle para chaves.
- Rodar `php -l` em lote.

2. Onda 2 (médio risco)
- Normalizar helpers e imports.
- Ajustar padrões mecânicos e repetitivos.

3. Onda 3 (alto risco)
- Revisar arquivos dinâmicos e fluxos críticos com validação manual.
- Aplicar mudanças semânticas pontuais apenas quando necessário.

## Fluxo padrão de execução
1. Dry-run
- `bash .github/skills/php-legacy-modernizer/scripts/fix_php_legacy_generic.sh --dry-run <diretorio>`

2. Revisão de diff
- Validar se mudanças são apenas mecânicas.

3. Apply
- `bash .github/skills/php-legacy-modernizer/scripts/fix_php_legacy_generic.sh --apply <diretorio>`

4. Lint
- Confirmar saída do `php -l` sem erros.

5. Revisão manual
- Checar arquivos com alta dinamicidade, includes condicionais e templates mistos.

## Critérios de aceite
- Zero erro de sintaxe (`php -l`) nos arquivos alterados.
- Ausência de regressão funcional nos fluxos críticos.
- Diffs pequenos, legíveis e auditáveis.

## Ferramentas recomendadas
- Genérico: `php-legacy-modernizer`
- Específico WorkControl: `workcontrol-legacy-fixer`
