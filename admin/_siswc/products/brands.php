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
        $Delete->exeDelete(DB_PDT_BRANDS_DORIPEL, "WHERE brand_title IS NULL AND brand_id >= :st", "st=1");
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-qrcode">Marcas ou Fabricantes</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/home">Produtos</a>
			<span class="crumb">/</span>
			Fabricantes
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Novo Fabricante" href="dashboard.php?wc=products/brand" class="btn btn_green icon-plus">Adicionar
			Fabricante!</a>
	</div>
</header>

<div class="dashboard_content">
    <?php
        $Read->exeRead(DB_PDT_BRANDS_DORIPEL, "ORDER By brand_title ASC");
        if (!$Read->getResult()) {
            echo Check::erro(
                "<span class='al_center icon-notification'>Ainda não existem marcas ou fabricantes cadastradas {$Admin['user_name']}. Comece agora mesmo criando suas marcas e fabricantes de produtos!</span>",
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Brand) {
                $Read->fullRead(
                    "SELECT count(pdt_id) as total FROM " . DB_PDT_DORIPEL . " WHERE pdt_brand = :brand",
                    "brand={$Brand['brand_id']}"
                );
                $TotalPdtBrand = $Read->getResult()[0]['total'];
                echo "<article class='product_brand box box100' id='{$Brand['brand_id']}'>
            
            <div class='box_content flex_box'>
               
               <h1 class='icon-qrcode'>{$Brand['brand_title']} <span>{$TotalPdtBrand} produto(s) encontrado(s)</span></h1>
                
               <div class='actions'>
                  <a title='Editar Fabricante!' href='dashboard.php?wc=products/brand&id={$Brand['brand_id']}' class='btn btn_blue icon-pencil icon-notext'></a>
                  <span rel='product_brand' class='j_delete_action btn btn_red icon-cancel-circle icon-notext' id='{$Brand['brand_id']}'></span>
                  <span rel='product_brand' callback='ProductsDoripel' callback_action='brand_remove' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none;' id='{$Brand['brand_id']}'>Excluir?</span>
			   </div>
            </div>
        </article>";
            }
        }
    ?>
</div>
