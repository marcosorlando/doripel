<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_PRODUCTS_DORIPEL;
    if (!APP_PRODUCTS_DORIPEL || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    if (empty($Read)) {
        $Read ??= new Read();
    }

    //AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH) {
        $Delete ??= new Delete();
        $Delete->exeDelete(
            DB_PDT_CATS_DORIPEL,
            "WHERE cat_title IS NULL AND cat_parent IS NULL AND cat_id >= :st",
            "st=1"
        );
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-price-tags">Categorias</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/home">Produtos</a>
			<span class="crumb">/</span>
			Categorias
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Nova Categoria" href="dashboard.php?wc=products/category" class="btn btn_green icon-plus">Adicionar
			Categoria!</a>
	</div>
</header>

<div class="dashboard_content">
    <?php
        $Read->exeRead(DB_PDT_CATS_DORIPEL, "WHERE cat_parent IS NULL ORDER BY cat_title ASC");
        if (!$Read->getResult()) {
            echo Check::erro(
                "<span class='al_center icon-notification'>Ainda não existem categorias de produtos cadastradas {$Admin['user_name']}. Comece agora mesmo criando seu primeiro setor, e então suas categorias!</span>",
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Sector) {
                $Read->fullRead(
                    "SELECT count(pdt_id) AS total FROM " . DB_PDT_DORIPEL . " WHERE pdt_category = :sector",
                    "sector={$Sector['cat_id']}"
                );
                $TotalPdtSector = $Read->getResult()[0]['total'];
                $Sector['cat_sizes'] = (!empty($Sector['cat_sizes']) ? $Sector['cat_sizes'] : 'default');

                echo "<article class='product_category box box100' id='{$Sector['cat_id']}'>
            <header class='flex_box header_yellow'>
                <h1 class='icon-price-tags'>{$Sector['cat_title']} <span>{$TotalPdtSector} produto(s) cadastrado(s)</span></h1>
               
                <div class='actions'>
                <a target='_blank' title='Ver Categoria!' href='" . BASE . "/moveis/{$Sector['cat_name']}' class='btn btn_green icon-eye icon-notext'></a>
                <a title='Editar Categoria!' href='dashboard.php?wc=products/category&id={$Sector['cat_id']}' class='btn btn_blue icon-pencil icon-notext'></a>
                <span rel='product_category' class='j_delete_action btn btn_red icon-cancel-circle icon-notext' id='{$Sector['cat_id']}'></span>
                <span rel='product_category' callback='ProductsDoripel' callback_action='cat_delete' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none;' id='{$Sector['cat_id']}'>Deletar Categoria?</span>
</div>
                
            </header>";

                $Read->exeRead(DB_PDT_CATS_DORIPEL, "WHERE cat_parent = :sector", "sector={$Sector['cat_id']}");
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $Cat) {
                        $Read->fullRead(
                            "SELECT count(pdt_id) AS total FROM " . DB_PDT_DORIPEL . " WHERE pdt_subcategory = :cat",
                            "cat={$Cat['cat_id']}"
                        );
                        $TotalPdtCat = $Read->getResult()[0]['total'];
                        $Cat['cat_sizes'] = (!empty($Cat['cat_sizes']) ? $Cat['cat_sizes'] : 'default');

                        echo "<article class='product_subcategory flex_box' id='{$Cat['cat_id']}'>
                            <h1 class='icon-price-tag'>{$Cat['cat_title']} <span>{$TotalPdtCat} produto(s) cadastrado(s)</span></h1>
                            
                            <div class='actions'>
                            <a target='_blank' title='Ver Categoria!' href='" . BASE . "/moveis/{$Cat['cat_name']}' class='btn btn_green icon-eye icon-notext'></a>
                            <a title='Editar Categoria!' href='dashboard.php?wc=products/category&id={$Cat['cat_id']}' class='btn btn_blue icon-pencil icon-notext'></a>
                            <span rel='product_subcategory' class='j_delete_action btn btn_red icon-cancel-circle icon-notext' id='{$Cat['cat_id']}'></span>
                            <span rel='product_subcategory' callback='ProductsDoripel' callback_action='cat_delete' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none;' id='{$Cat['cat_id']}'>Deletar Categoria?</span>
</div>
                        </article>";
                    }
                }
                echo "</article>";
            }
        }
    ?>
</div>
