# Instruções Globais do Projeto

Este projeto é um sistema legado em PHP vanilla, em modernização para PHP 8.5.3.

## Objetivo principal

Modernizar o sistema gradualmente, com segurança, sem introduzir frameworks, aumentando aderência a padrões PSR e reduzindo acoplamento do legado.

## Regras obrigatórias

- Não introduzir frameworks.
- Preservar comportamento de negócio.
- Priorizar refatoração incremental.
- Aplicar PSR-1, PSR-4 e PSR-12 quando viável.
- Sugerir tipagem forte apenas quando seguro.
- Não reescrever módulos grandes sem necessidade.
- Explicar riscos de compatibilidade antes de mudanças amplas.
- Preferir clareza a abstrações desnecessárias.

## Ao editar PHP

- Verificar compatibilidade com PHP 8.5.3.
- Identificar funções removidas, comportamentos obsoletos e padrões perigosos.
- Organizar namespaces e imports.
- Melhorar legibilidade sem alterar semântica.
- Manter arquitetura PHP vanilla.

## Seleção de skill e agent

- Para modernização genérica de PHP legado, priorizar `php-legacy-modernizer`.
- Para módulos com convenções internas do WorkControl, usar `workcontrol-legacy-fixer`.
- Em dúvidas, começar por `php-legacy-modernizer` e trocar para `workcontrol-legacy-fixer` apenas quando surgirem regras internas específicas (helpers, CRUD padrão, bootstrap legado do domínio).

## Fluxo recomendado com `php-legacy-modernizer`

1. Executar `--dry-run`.
2. Revisar diff com foco em mudanças mecânicas.
3. Executar `--apply`.
4. Validar `php -l`.
5. Reportar riscos e pontos de revisão manual.

## Ao sugerir refatorações

Sempre priorizar:
1. segurança
2. compatibilidade
3. legibilidade
4. separação de responsabilidades
5. padronização PSR

## Evitar

- overengineering
- reescrita completa sem solicitação
- criação de camadas artificiais
- troca de convenções internas já consolidadas sem justificar
