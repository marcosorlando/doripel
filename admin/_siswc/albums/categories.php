<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_PRODUCTS;
    if (!APP_PRODUCTS_TRAVI || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    // AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(DB_PDT_CATS, 'WHERE cat_title IS NULL AND cat_parent IS NULL AND cat_id >= :st', 'st=1');
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-price-tags">Categorias</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=products/home">Produtos</a>
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
        $Read->exeRead(DB_PDT_CATS, 'WHERE cat_parent IS NULL ORDER BY cat_title ASC');
        if (!$Read->getResult()) {
            echo Check::erro(
                \sprintf(
                    "<span class='al_center icon-notification'>Ainda não existem categorias de produtos cadastradas %s. Comece agora mesmo criando seu primeiro setor, e então suas categorias!</span>",
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Sector) {
                $Read->fullRead(
                    'SELECT count(pdt_id) AS total FROM ' . DB_PDT . ' WHERE pdt_category = :sector',
                    'sector=' . $Sector['cat_id']
                );
                $TotalPdtSector = $Read->getResult()[0]['total'];
                $Sector['cat_sizes'] = (empty($Sector['cat_sizes']) ? 'default' : $Sector['cat_sizes']);

                echo "<article class='product_category box box100' id='{$Sector['cat_id']}'>
            <header>
                <h1 class='icon-price-tags'>{$Sector['cat_title']} <span>{$TotalPdtSector} produto(s) cadastrado(s)</span> <span>[{$Sector['cat_sizes']}]</span></h1>
                <a target='_blank' title='Ver Categoria!' href='" . BASE . "/produtos/{$Sector['cat_name']}' class='btn btn_green icon-eye icon-notext'></a>
                <a title='Editar Categoria!' href='dashboard.php?wc=products/category&id={$Sector['cat_id']}' class='btn btn_blue icon-pencil icon-notext'></a>
                <span rel='product_category' class='j_delete_action btn btn_red icon-cancel-circle icon-notext' id='{$Sector['cat_id']}'></span>
                <span rel='product_category' callback='Products' callback_action='cat_delete' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none;' id='{$Sector['cat_id']}'>Deletar Categoria?</span>
            </header>";

                $Read->exeRead(DB_PDT_CATS, 'WHERE cat_parent = :sector', 'sector=' . $Sector['cat_id']);
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $Cat) {
                        $Read->fullRead(
                            'SELECT count(pdt_id) AS total FROM ' . DB_PDT . ' WHERE pdt_subcategory = :cat',
                            'cat=' . $Cat['cat_id']
                        );
                        $TotalPdtCat = $Read->getResult()[0]['total'];
                        $Cat['cat_sizes'] = (empty($Cat['cat_sizes']) ? 'default' : $Cat['cat_sizes']);

                        echo "<article class='product_subcategory' id='{$Cat['cat_id']}'>
                            <h1 class='icon-price-tag'>{$Cat['cat_title']} <span>{$TotalPdtCat} produto(s) cadastrado(s)</span> <span>[{$Cat['cat_sizes']}]</span></h1>
                            <a target='_blank' title='Ver Categoria!' href='" . BASE . "/produtos/{$Cat['cat_name']}' class='btn btn_green icon-eye icon-notext'></a>
                            <a title='Editar Categoria!' href='dashboard.php?wc=products/category&id={$Cat['cat_id']}' class='btn btn_blue icon-pencil icon-notext'></a>
                            <span rel='product_subcategory' class='j_delete_action btn btn_red icon-cancel-circle icon-notext' id='{$Cat['cat_id']}'></span>
                            <span rel='product_subcategory' callback='Products' callback_action='cat_delete' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none;' id='{$Cat['cat_id']}'>Deletar Categoria?</span>
                        </article>";
                    }
                }
                echo '</article>';
            }
        }
    ?>
</div>
