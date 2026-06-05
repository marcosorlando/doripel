# WorkControl Legacy Patterns

## Objetivo
Documentar padrões internos usados pela skill `workcontrol-legacy-fixer`.

## Convenções internas

### BOOTSTRAP
Quando encontrar:
- legado:
    - `_app/Config.inc.php`
- padrão atual:
    - `vendor/autoload.php`

### Helper principal
- Classe: `App\Helpers\Check`
- Deve concentrar helpers antes expostos como functions globais

### Helper para tratar Datas
- Classe: `App\Helpers\DateHelper`
- Deve concentrar helpers relacionadas a datas antes expostos como functions globais

### Erros AJAX
- legado:
  - `Erro(...)`
  - `ajaxErro(...)`
- padrão atual:
  - `Check::ajaxErro(...)`

### CRUD objects
Padrão aceito:
- `$Read`
- `$Create`
- `$Update`
- `$Delete`
- `$Upload`

#### Quando necessário:
    ```php

    $Read ??= new Read();
    $Create ??= new Create();
    $Update ??= new Update();
    $Delete ??= new Delete();
    $Upload ??= new Upload();

    ```php

## Atenção

- Aplicar substituições automáticas apenas quando houver segurança semântica suficiente. Arquivos altamente dinâmicos devem ser revisados manualmente.
