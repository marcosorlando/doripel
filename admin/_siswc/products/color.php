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

    $ColorId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($ColorId) {
        $Read->exeRead(DB_PDT_COLORS_DORIPEL, "WHERE color_id = :id", "id={$ColorId}");
        if ($Read->getResult()) {
            $FormData = array_map(static fn($value) => Check::safeHtmlChars($value), $Read->getResult()[0]);
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = Check::erro(
                "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um Padrão/Cor que não existe ou que foi removido recentemente!",
                E_USER_NOTICE
            );
            header('Location: dashboard.php?wc=products/brands');
            exit;
        }
    } else {
        $Date = date('Y-m-d H:i:s');
        $Title = "Novo Padrão/Cor - {$Date}";
        $Name = Check::name($Title);
        $ColorCreate = ['color_name' => $Name, 'color_created' => $Date];
        $Create->exeCreate(DB_PDT_COLORS_DORIPEL, $ColorCreate);
        header('Location: dashboard.php?wc=products/color&id=' . $Create->getResult());
        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-paint-format"><?= $color_title ? $color_title : 'Novo Padrão/Cor'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/home">Produtos</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/colors">Padrão/Cor</a>
			<span class="crumb">/</span>
			Gerenciar Padrões
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Ver Padrão/Cors!" href="dashboard.php?wc=products/colors" class="btn btn_blue icon-eye">Ver todas</a>
		<a title="Novo Padrão/Cors" href="dashboard.php?wc=products/color" class="btn btn_green icon-plus">Adicionar</a>
	</div>

</header>

<div class="dashboard_content">
	<div class="box box100">

		<div class="panel_header default">
			<h2 class="icon-paint-format">Padrão/Cor</h2>
		</div>
		<div class="panel">
			<form class="auto_save" name="color_add" action="" method="post" enctype="multipart/form-data">
				<div class="callback_return"></div>
				<input type="hidden" name="callback" value="ProductsDoripel"/>
				<input type="hidden" name="callback_action" value="color_manager"/>
				<input type="hidden" name="color_id" value="<?= $ColorId; ?>"/>
				<label class="label">
					<span class="legend">Padrão/Cor:</span>
					<input style="font-size: 1.5em;" type="text" name="color_title" value="<?= $color_title; ?>"
					       placeholder="Nome do Padrão/Cor:" required/>
				</label>

				<div class="m_top">&nbsp;</div>
				<img class="form_load fl_right none" style="margin-left: 10px; margin-top: 2px;"
				     alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
				<button class="btn btn_green icon-price-tags fl_right">Atualizar Padrão/Cor!</button>
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
