<?php

    use App\Conn\Create;
    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_PRODUCTS_DORIPEL;
    if (!APP_PRODUCTS_DORIPEL || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
        Check::accessBlocked();
    endif;

    // AUTO INSTANCE OBJECT READ
    if (empty($Read)):
        $Read = new Read;
    endif;

    //AUTO DELETE PRODUCT TRASH
    if (DB_AUTO_TRASH):
        $Delete = new Delete;
        $Delete->exeDelete(
            DB_PDT_DORIPEL,
            "WHERE pdt_title IS NULL AND pdt_content IS NULL and pdt_status = :st",
            "st=0"
        );

        //AUTO TRASH IMAGES
        $Read->fullRead(
            "SELECT image FROM " . DB_PDT_IMAGE_DORIPEL . " WHERE product_id NOT IN(SELECT pdt_id FROM " . DB_PDT_DORIPEL . ")"
        );
        if ($Read->getResult()):
            $Delete->exeDelete(
                DB_PDT_IMAGE_DORIPEL,
                "WHERE id >= :id AND product_id NOT IN(SELECT pdt_id FROM " . DB_PDT_DORIPEL . ")",
                "id=1"
            );
            foreach ($Read->getResult() as $ImageRemove):
                if (file_exists("../uploads/{$ImageRemove['image']}") && !is_dir("../uploads/{$ImageRemove['image']}")):
                    unlink("../uploads/{$ImageRemove['image']}");
                endif;
            endforeach;
        endif;
    endif;

    // AUTO INSTANCE OBJECT CREATE
    if (empty($Create)):
        $Create = new Create;
    endif;

    $S = filter_input(INPUT_GET, "s", FILTER_DEFAULT);
    $O = filter_input(INPUT_GET, "opt", FILTER_DEFAULT);

    $WhereString = "";
    $WhereParams = "";
    if (!empty($S)):
        $WhereString = " AND (pdt_title LIKE :search OR pdt_content LIKE :search OR pdt_code = :code) ";
        $WhereParams = http_build_query(['search' => "%{$S}%", 'code' => $S]);
    endif;
    $WhereOpt = ((!empty($O)) ? " AND (pdt_status != 1) " : "");

    $Search = filter_input_array(INPUT_POST);
    if ($Search):
        $S = urlencode((string)($Search['s'] ?? ''));
        $O = urlencode((string)($Search['opt'] ?? ''));
        header("Location: dashboard.php?wc=products/home&opt={$O}&s={$S}");
        exit;
    endif;

    $RedirectOpt = (!empty($WhereOpt) ? "&opt=outsale" : "");
    $RedirectSearch = (!empty($S) ? '&s=' . urlencode((string)$S) : '');
    $Page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    $Page = (is_int($Page) && $Page > 0) ? $Page : 1;
    $Pager = new Pager("dashboard.php?wc=products/home{$RedirectOpt}{$RedirectSearch}&page=", "<<", ">>", 5);
    $Pager->exePager($Page, 12);

    $ReadParams = http_build_query(['limit' => $Pager->getLimit(), 'offset' => $Pager->getOffset()]);
    $ReadParams .= ('' !== $WhereParams ? '&' . $WhereParams : '');

    $Read->exeRead(
        DB_PDT_DORIPEL,
        "WHERE 1 = 1 $WhereString $WhereOpt ORDER BY pdt_created DESC LIMIT :limit OFFSET :offset",
        $ReadParams
    );
    $ProductsResult = $Read->getResult();

    if (!$ProductsResult && $Page > 1):
        $Pager->returnPage();
    endif;
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-books">Produtos</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Produtos
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchPosts" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" name="s" placeholder="Pesquisar:" style="width: 38%; margin-right: 3px;"/>
			<select name="opt" style="width: 45%; margin-right: 3px; padding: 5px 10px">
				<option value="">Todos</option>
				<option <?= ($O == 'outsale' ? "selected='selected'" : ''); ?> value="outsale">Indisponíveis</option>
			</select>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>

</header>
<div class="dashboard_content">
    <?php
        if (!$ProductsResult):
            echo Check::erro(
                "Ainda não existem produtos cadastrados ou filtro não obteve resultados.",
                E_USER_NOTICE
            );
        else:
            foreach ($ProductsResult as $Products):
                extract($Products);
                $PdtImage = ($pdt_cover && file_exists("../uploads/{$pdt_cover}") && !is_dir(
                    "../uploads/{$pdt_cover}"
                ) ? "uploads/{$pdt_cover}" : 'admin/_img/no_image.jpg');
                $PdtTitle = ($pdt_title ? Check::chars($pdt_title, 45) : 'Edite este produto para coloca-lo a venda!');
                $PdtCode = ($pdt_code ? $pdt_code : 'indefinido');
                $PdtRef = ($pdt_ref ? $pdt_ref : 'indefinido');
                $PdtClass = ($pdt_status != 1 ? 'inactive' : '');
//            $PdtClass = ($pdt_status != 1 ? 'inactive' : (is_numeric($pdt_inventory) && $pdt_inventory <= 0 ? 'outsale' : ''));
                echo "<article class='box box25 single_pdt {$PdtClass}' id='{$pdt_id}'>
           
            <div class='single_pdt_thumb'>
            <img title='{$PdtTitle}' alt='{$PdtTitle}' src='../tim.php?src={$PdtImage}&w=" . THUMB_W . "&h=" . THUMB_H . "'/>
                <header>
                    <h1><a target='_blank' href='" . BASE . "/movel/{$pdt_name}' title='Ver {$PdtTitle} no site'>{$PdtTitle}</a></h1>";

                $Read->fullRead(
                    "SELECT brand_title FROM " . DB_PDT_BRANDS_DORIPEL . " WHERE brand_id = :bid",
                    "bid={$pdt_brand}"
                );
                $Brand = ($Read->getResult() ? $Read->getResult()[0]['brand_title'] : 'indefinida');

                $Read->fullRead(
                    "SELECT cat_title FROM " . DB_PDT_CATS_DORIPEL . " WHERE cat_id = :cat",
                    "cat={$pdt_category}"
                );
                $Category = ($Read->getResult() ? $Read->getResult()[0]['cat_title'] : 'indefinida');

                $Read->fullRead(
                    "SELECT cat_title FROM " . DB_PDT_CATS_DORIPEL . " WHERE cat_id = :cat",
                    "cat={$pdt_subcategory}"
                );
                $SubCategory = ($Read->getResult() ? $Read->getResult()[0]['cat_title'] : 'indefinida');

                $PdtSoldVar = null;
                $PdtStockVar = null;
                $Read->fullRead(
                    "SELECT stock_code, stock_inventory, stock_sold FROM " . DB_PDT_STOCK_DORIPEL . " WHERE pdt_id = :id",
                    "id={$pdt_id}"
                );
                if ($Read->getResult()):
                    foreach ($Read->getResult() as $StockVarKey):
                        if ($StockVarKey['stock_code'] != 'default'):
                            $PdtSoldVar .= " | {$StockVarKey['stock_code']}: {$StockVarKey['stock_sold']}";
                            $PdtStockVar .= " | {$StockVarKey['stock_code']}: {$StockVarKey['stock_inventory']}";
                        endif;
                    endforeach;
                else:
                    //RETRO COMPATIBILIDADE WC
                    $CreateStock = [
                        'pdt_id' => $pdt_id,
                        'stock_code' => 'default',
                        'stock_inventory' => $pdt_inventory

                    ];
                    $Create->exeCreate(DB_PDT_STOCK_DORIPEL, $CreateStock);
                endif;

                echo "</header>
            </div>
            <div class='box_content'>
                <div class='single_pdt_info wc_normalize_height'>
                    <p>REF: <b>{$PdtRef}</b> | Código: <b>{$PdtCode}</b></p>" .

                    "<p>Cor/Padrão: <b>{$pdt_color}</b></p>
                  <p>Fabricante: <b>{$Brand}</b></p>
                    <p>Em: <b>{$Category}</b> &raquo; <b>{$SubCategory}</b></p>
                </div>
            
            <div class='single_pdt_actions'>
                <a title='Editar produto' href='dashboard.php?wc=products/create&id={$pdt_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                <span rel='single_pdt' class='j_delete_action icon-cancel-circle btn btn_red' id='{$pdt_id}'>Excluir</span>
                <span rel='single_pdt' callback='ProductsDoripel' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$pdt_id}'>Remover?</span>
            </div>
            </div>
        </article>";
            endforeach;

            $Pager->exePaginator(
                DB_PDT_DORIPEL,
                "WHERE 1 = 1 {$WhereString} {$WhereOpt}",
                ('' !== $WhereParams ? $WhereParams : null)
            );
            echo $Pager->getPaginator();

        endif;
    ?>
</div>
