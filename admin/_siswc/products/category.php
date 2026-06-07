<?php

    use App\Conn\Create;
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

    // AUTO INSTANCE OBJECT CREATE
    if (empty($Create)) {
        $Create ??= new Create();
    }

    $CatId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($CatId) {
        $Read->exeRead(DB_PDT_CATS_DORIPEL, "WHERE cat_id = :id", "id={$CatId}");
        if ($Read->getResult()) {
            $FormData = array_map(static fn($value) => Check::safeHtmlChars($value), $Read->getResult()[0]);
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = Check::erro(
                "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar uma categoria que não existe ou que foi removida recentemente!",
                E_USER_NOTICE
            );
            header('Location: dashboard.php?wc=products/categories');
            exit;
        }
    } else {
        $Date = date('Y-m-d H:i:s');
        $Title = "Nova Categoria - {$Date}";
        $Name = Check::name($Title);
        $CarCreate = ['cat_name' => $Name, 'cat_created' => $Date];
        $Create->exeCreate(DB_PDT_CATS_DORIPEL, $CarCreate);
        header('Location: dashboard.php?wc=products/category&id=' . $Create->getResult());
        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-price-tags"><?= $cat_title ? $cat_title : 'Nova Categoria'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/home">Produtos</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/categories">Categorias</a>
			<span class="crumb">/</span>
			Gerenciar Categoria
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Ver Categorias!" href="dashboard.php?wc=products/categories" class="btn btn_blue icon-eye">Ver
			todas</a>
		<a title="Nova Categoria" href="dashboard.php?wc=products/category"
		   class="btn btn_green icon-plus">Adicionar</a>
	</div>

</header>

<div class="dashboard_content">
	<div class="box box100">

		<div class="panel_header default">
			<h2 class="icon-price-tags">Dados da Categoria</h2>
		</div>

		<div class="panel">
			<form class="auto_save" name="category_add" action="" method="post" enctype="multipart/form-data">
				<div class="callback_return"></div>
				<input type="hidden" name="callback" value="ProductsDoripel"/>
				<input type="hidden" name="callback_action" value="cat_manager"/>
				<input type="hidden" name="cat_id" value="<?= $CatId; ?>"/>
				<label class="label">
					<span class="legend">Nome:</span>
					<input style="font-size: 1.5em;" type="text" name="cat_title" value="<?= $cat_title; ?>"
					       placeholder="Título da Categoria:" required/>
				</label>

				<label class="label">
					<span class="legend">Setor:</span>
					<select name="cat_parent" class="jwc_pdtsection_selector">
						<option value="">Esse é um setor!</option>
                        <?php
                            $Read->fullRead(
                                "SELECT cat_id, cat_title, cat_sizes FROM " . DB_PDT_CATS_DORIPEL . " WHERE cat_parent IS NULL AND cat_id != :ci ORDER BY cat_title ASC",
                                "ci={$CatId}"
                            );
                            if ($Read->getResult()) {
                                foreach ($Read->getResult() as $Sector) {
                                    echo "<option class='{$Sector['cat_sizes']}'";
                                    if ($Sector['cat_id'] == $cat_parent) {
                                        echo " selected='selected'";
                                    }
                                    echo " value='{$Sector['cat_id']}'>&raquo;{$Sector['cat_title']}</option>";
                                }
                            }
                        ?>
					</select>
				</label>

				<!--                <label class="label">-->
				<!--                    <span class="legend">Tamanhos separados por vírgula:</span>-->
				<!--                    <input class="jwc_pdtsection_selector_target" type="text" name="cat_sizes" value="-->
                <?
                    //= $cat_sizes; ?><!--" placeholder="Ex: P,M,G,GG"/>-->
				<!--                </label>-->

				<div class="m_top">&nbsp;</div>
				<img class="form_load fl_right none" style="margin-left: 10px; margin-top: 2px;"
				     alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
				<button class="btn btn_green icon-price-tags fl_right">Atualizar Categoria!</button>
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
