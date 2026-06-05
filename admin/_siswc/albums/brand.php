<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_PRODUCTS;
    if (!APP_PRODUCTS_TRAVI || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $BrandId = \filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($BrandId) {
        $Read->exeRead(DB_PDT_BRANDS, 'WHERE brand_id = :id', 'id=' . $BrandId);
        if ($Read->getResult()) {
            $FormData = \array_map(
                fn($v) => \htmlspecialchars((string)(\is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            \extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = Check::erro(
                \sprintf(
                    '<b>OPPSS %s</b>, você tentou editar um fabricante que não existe ou que foi removido recentemente!',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
            \header('Location: dashboard.php?wc=products/brands');
        }
    } else {
        $Date = \date('Y-m-d H:i:s');
        $Title = 'Novo Fabricante - ' . $Date;
        $Name = Check::name($Title);
        $CarCreate = ['brand_name' => $Name, 'brand_created' => $Date];
        $Create->exeCreate(DB_PDT_BRANDS, $CarCreate);
        \header('Location: dashboard.php?wc=products/brand&id=' . $Create->getResult());
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-price-tags"><?php
                echo $brand_title ? $brand_title : 'Nova Marca ou Fabricante'; ?></h1>
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
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=products/brands">Fabricantes</a>
			<span class="crumb">/</span>
			Gerenciar Marca/Fabricante
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Ver Fabricantes!" href="dashboard.php?wc=products/brands" class="btn btn_blue icon-eye">Ver
			Fabricantes!</a>
		<a title="Novo Fabricantes" href="dashboard.php?wc=products/brand" class="btn btn_green icon-plus">Adicionar
			Fabricante!</a>
	</div>

</header>

<div class="dashboard_content">
	<div class="box box100">

		<div class="panel_header default">
			<h2 class="icon-price-tags">Dados da Marca/Fabricante</h2>
		</div>
		<div class="panel">
			<form class="auto_save" name="category_add" action="" method="post" enctype="multipart/form-data">
				<div class="callback_return"></div>
				<input type="hidden" name="callback" value="Products"/>
				<input type="hidden" name="callback_action" value="brand_manager"/>
				<input type="hidden" name="brand_id" value="<?php
                    echo $BrandId; ?>"/>
				<label class="label">
					<span class="legend">Marca/Fabricante:</span>
					<input style="font-size: 1.5em;" type="text" name="brand_title" value="<?php
                        echo $brand_title; ?>"
					       placeholder="Nome do Fabricante:" required/>
				</label>

				<div class="m_top">&nbsp;</div>
				<img class="form_load fl_right none" style="margin-left: 10px; margin-top: 2px;"
				     alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
				<button class="btn btn_green icon-price-tags fl_right">Atualizar Fabricante!</button>
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
