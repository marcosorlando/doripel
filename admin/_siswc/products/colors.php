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
        $Delete->exeDelete(DB_PDT_COLORS_DORIPEL, "WHERE color_title IS NULL AND color_id >= :st", "st=1");
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-paint-format">Padrões ou Cores</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/home">Produtos</a>
			<span class="crumb">/</span>
			Cores e Padrões
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Novo Padrão/Cor" href="dashboard.php?wc=products/color" class="btn btn_green icon-plus">Adicionar
			Padrão/Cor</a>
	</div>
</header>

<div class="dashboard_content">
    <?php
        $Read->exeRead(DB_PDT_COLORS_DORIPEL, "ORDER By color_title ASC");
        if (!$Read->getResult()) {
            echo Check::erro(
                "<span class='al_center icon-notification'>Ainda não existem padrões de cores cadastradas {$Admin['user_name']}. Comece agora mesmo criando seus padrões de cores!</span>",
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Color) {
                $Read->fullRead(
                    "SELECT count(pdt_id) as total FROM " . DB_PDT_DORIPEL . " WHERE pdt_color = :color",
                    "color={$Color['color_id']}"
                );
                $TotalPdtColor = $Read->getResult()[0]['total'];
                echo "<article class='product_color box box100' id='{$Color['color_id']}'>
            <div class='box_content flex_box'>
                <h1 class='icon-droplet'>{$Color['color_title']} <span>{$TotalPdtColor} produto(s) encontrado(s)</span></h1>
                
                <div class='actions'>
               
                <a title='Editar Padrão/Cor!' href='dashboard.php?wc=products/color&id={$Color['color_id']}' class='btn btn_blue icon-pencil icon-notext'></a>
                <span rel='product_color' class='j_delete_action btn btn_red icon-cancel-circle icon-notext' id='{$Color['color_id']}'></span>
                <span rel='product_color' callback='ProductsDoripel' callback_action='color_remove' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none;' id='{$Color['color_id']}'>Deletar Padrão/Cor?</span>
</div>
            </div>
        </article>";
            }
        }
    ?>
</div>
