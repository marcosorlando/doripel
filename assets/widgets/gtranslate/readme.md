# INSTALAÇÃO no WC

## PASSO 1

Cole a pasta **gtranslate** no seguinte caminho:
assets/gtranslate_

## PASSO 2

Encontre o arquivo:  themes/SEU-TEMA/inc/header.php e adicione a seguinte linha logo no inicio do arquivo dentro de
tags PHP.

```
<?php
  require cdn/gtranslate/gtranslate.php;
?>
```

---

# INSTALAÇÃO SEM SER WC

## PASSO 1

Cole a pasta **gtranslate** dentro do seu projeto.

## PASSO 2

Encontre arquivo que seja comum a todas as páginas do site (Ex. header, menu) e através de um REQUIRE adicione o
arquivo _gtranslate.php_

```
<?php
  require SEU-CAMINHO/gtranslate.php;
?>
```
