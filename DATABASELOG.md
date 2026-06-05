# Alterar CHARSET e COLLATE Banco de dados e tabelas individuais

## Siga os passo abaixo:

### Passo 1: Altera no Banco Geral

ALTER DATABASE 'db_name'
CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci

### Passo 2: Gera Listagem das tabelas com o ALTER TABLE para vc executar em seguida

SELECT
CONCAT('ALTER TABLE `', TABLE_NAME, '` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;')
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'db_name'

### Passo 3: Vai gerar uma listagem com os comandos para alterar em todas as tabelas (semelhante ex. abaixo), copie a saída e cole no linha de comando SQL e execute, para aplicar.

ALTER TABLE `ws_works_categories` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
ALTER TABLE `workcontrol_code` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
ALTER TABLE `ws_segments_images` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

### Passo:4 Por fim confira se todas as tabelas ficaram com o COLLATE desejado

SHOW TABLE STATUS

# ==========================================

# Alterar extensão de imagens no banco para .webp, primeira crie uma action no Photoshop para otimizar as imagens antes de mudar no banco.

## tabela ws_posts_images

### Valida antes de atualizar

SELECT
id,
image AS imagem_atual,
REGEXP_REPLACE(image, '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$', '.webp') AS nova_imagem
FROM `ws_posts_images` WHERE image IS NOT NULL
AND image <> ''
AND image REGEXP '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$';

### Aplica update na tabela

UPDATE ws_posts_images
SET image = REGEXP_REPLACE(image, '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$', '.webp')
WHERE image IS NOT NULL
AND image <> ''
AND image REGEXP '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$';

## tabela ws_posts

### Valida antes de atualizar

SELECT
post_id,
post_cover AS imagem_atual,
REGEXP_REPLACE(post_cover, '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$', '.webp') AS nova_imagem
FROM `ws_posts` WHERE post_cover IS NOT NULL
AND post_cover <> ''
AND post_cover REGEXP '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$';

### Aplica update na tabela

UPDATE 'ws_posts'
SET post_cover = REGEXP_REPLACE(post_cover, '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$', '.webp')
WHERE post_cover IS NOT NULL
AND post_cover <> ''
AND post_cover REGEXP '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$';

## tabela ws_users

### Valida antes de atualizar

SELECT
user_id,
user_thumb AS imagem_atual,
REGEXP_REPLACE(user_thumb, '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$', '.webp') AS nova_imagem
FROM `ws_users` WHERE user_thumb IS NOT NULL
AND user_thumb <> ''
AND user_thumb REGEXP '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$';

### Aplica update na tabela

UPDATE ws_users
SET user_thumb = REGEXP_REPLACE(user_thumb, '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$', '.webp')
WHERE user_thumb IS NOT NULL
AND user_thumb <> ''
AND user_thumb REGEXP '\\.(jpg|jpeg|png|gif|bmp|avif|webp)$';

CREATE TABLE `portfolio` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`title` varchar(255) DEFAULT NULL,
`slug` varchar(255) DEFAULT NULL,
`description` text DEFAULT NULL,
`client` varchar(100) DEFAULT NULL,
`link_project` varchar(150) DEFAULT NULL,
`skills` varchar(200) DEFAULT NULL,
`key_metrics` varchar(200) DEFAULT NULL,
`measurement_period` varchar(200) DEFAULT NULL,
`problem` varchar(200) DEFAULT NULL,
`objectives` varchar(200) DEFAULT NULL,
`niche` varchar(200) DEFAULT NULL,
`project_duration` varchar(200) DEFAULT NULL,
`deliveryted_at` timestamp NULL DEFAULT NULL,
`category` smallint(6) DEFAULT NULL,
`author` int(11) unsigned NOT NULL,
`img_970x500` varchar(245) DEFAULT NULL,
`img_450x350` varchar(245) DEFAULT NULL,
`img_350x350` varchar(245) DEFAULT NULL,
`views` int(11) DEFAULT NULL,
`lastview` datetime DEFAULT NULL,
`show_client` tinyint(1) DEFAULT 1,
`highlight_case` tinyint(1) DEFAULT 0,
`status` tinyint(1) DEFAULT 0,
`created_at` timestamp NULL DEFAULT current_timestamp(),
`updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
PRIMARY KEY (`id`),
UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

## Produtos Doripel - volumes por produto

```sql
CREATE TABLE IF NOT EXISTS `ws_products_volumes_doripel` (
  `volume_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pdt_id` int(11) unsigned NOT NULL,
  `volume_order` int(11) unsigned NOT NULL DEFAULT 1,
  `volume_weight` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `volume_depth` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `volume_width` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `volume_height` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `volume_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `volume_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`volume_id`),
  UNIQUE KEY `uniq_product_order` (`pdt_id`, `volume_order`),
  KEY `idx_product` (`pdt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```
