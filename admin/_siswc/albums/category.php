<?php

    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_PRODUCTS;
    if (!APP_PRODUCTS_DORIPEL || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $CatId = \filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($CatId) {
        $Read->exeRead(DB_PDT_CATS, 'WHERE cat_id = :id', 'id=' . $CatId);
        if ($Read->getResult()) {
            $FormData = \array_map(
                fn($v) => \htmlspecialchars((string)(\is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            \extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = Check::erro(
                \sprintf(
                    '<b>OPPSS %s</b>, você tentou editar uma categoria que não existe ou que foi removida recentemente!',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
            \header('Location: dashboard.php?wc=products/categories');
        }
    } else {
        $Date = \date('Y-m-d H:i:s');
        $Title = 'Nova Categoria - ' . $Date;
        $Name = Check::name($Title);
        $CarCreate = ['cat_name' => $Name, 'cat_created' => $Date];
        $Create->exeCreate(DB_PDT_CATS, $CarCreate);
        \header('Location: dashboard.php?wc=products/category&id=' . $Create->getResult());
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-price-tags"><?php
                echo $cat_title ? $cat_title : 'Nova Categoria'; ?></h1>
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
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=products/categories">Categorias</a>
			<span class="crumb">/</span>
			Gerenciar Categoria
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Ver Categorias!" href="dashboard.php?wc=products/categories" class="btn btn_blue icon-eye">Ver
			Categorias!</a>
		<a title="Nova Categoria" href="dashboard.php?wc=products/category" class="btn btn_green icon-plus">Adicionar
			Categoria!</a>
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
				<input type="hidden" name="callback" value="Products"/>
				<input type="hidden" name="callback_action" value="cat_manager"/>
				<input type="hidden" name="cat_id" value="<?php
                    echo $CatId; ?>"/>
				<label class="label">
					<span class="legend">Nome:</span>
					<input style="font-size: 1.5em;" type="text" name="cat_title" value="<?php
                        echo $cat_title; ?>"
					       placeholder="Título da Categoria:" required/>
				</label>

				<label class="label">
					<span class="legend">Setor:</span>
					<select name="cat_parent" class="jwc_pdtsection_selector">
						<option value="">Esse é um setor!</option>
                        <?php
                            $Read->fullRead(
                                'SELECT cat_id, cat_title, cat_sizes FROM ' . DB_PDT_CATS . ' WHERE cat_parent IS NULL AND cat_id != :ci ORDER BY cat_title ASC',
                                'ci=' . $CatId
                            );
                            if ($Read->getResult()) {
                                foreach ($Read->getResult() as $Sector) {
                                    echo \sprintf("<option class='%s'", $Sector['cat_sizes']);
                                    if ($Sector['cat_id'] == $cat_parent) {
                                        echo " selected='selected'";
                                    }
                                    echo \sprintf(
                                        " value='%s'>&raquo;%s</option>",
                                        $Sector['cat_id'],
                                        $Sector['cat_title']
                                    );
                                }
                            }
                        ?>
					</select>
				</label>

				<label class="label">
					<span class="legend">Tamanhos separados por vírgula:</span>
					<input class="jwc_pdtsection_selector_target" type="text" name="cat_sizes"
					       value="<?php
                               echo $cat_sizes; ?>" placeholder="Ex: P,M,G,GG"/>
				</label>

				<div class="m_top">&nbsp;</div>
				<img class="form_load fl_right none" style="margin-left: 10px; margin-top: 2px;"
				     alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
				<button class="btn btn_green icon-price-tags fl_right">Atualizar Categoria!</button>
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
