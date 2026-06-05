# Padrões de transformação — php-legacy-modernizer

## Escopo seguro (mecânico)

- converter sintaxe alternativa para chaves:
  - `if/elseif/else/endif`
  - `foreach/endforeach`
  - `for/endfor`
  - `while/endwhile`
  - `switch/endswitch`
- remover duplicidade de encerramento legado:
  - `endforeach;;` -> `endforeach;` antes da conversão para chaves
- rodar `php -l` em todos os arquivos `.php` do diretório-alvo

## Sinais de atenção (revisão manual obrigatória)

- arquivos com mistura intensa de HTML + PHP em blocos condicionais longos
- templates com `echo` de trechos grandes contendo `:` e `endif` em strings
- arquivos com geração dinâmica de código PHP
- módulos com includes condicionais complexos e side-effects globais

## Regras de segurança

- não alterar regra de negócio
- não introduzir framework
- não substituir helpers específicos de domínio por conta própria
- não aplicar normalizações de convenções internas do WorkControl nesta skill

## Encaminhamento

Quando o módulo exigir convenções internas (helpers `Check`, padrão CRUD, bootstrap legado do domínio), usar `workcontrol-legacy-fixer`.
