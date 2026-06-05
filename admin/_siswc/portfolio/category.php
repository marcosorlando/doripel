<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    $adminLevel = 6;
    if (!APP_PORTFOLIO || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $adminLevel) {
        Check::accessBlocked();
    }

    $Read ??= new Read();
    $Create ??= new Create();

    $catId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($catId) {
        $Read->exeRead(DB_PORTFOLIO_CATEGORIES, "WHERE id = :id", "id={$catId}");
        if ($Read->getResult()) {
            $formData = array_map(static fn($value) => Check::safeHtmlChars($value), $Read->getResult()[0]);
            extract($formData);
        } else {
            $_SESSION['trigger_controll'] = Check::ajaxErro(
                "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar uma categoria que não existe ou que foi removida recentemente!",
                E_USER_NOTICE
            );
            header('Location: dashboard.php?wc=portfolio/categories');
            exit;
        }
    } else {
        $date = date('Y-m-d H:i:s');
        $title = "Nova Categoria - {$date}";
        $name = Check::name($title);
        $catCreate = ['name' => $name, 'date' => $date];
        $Create->exeCreate(DB_PORTFOLIO_CATEGORIES, $catCreate);
        header('Location: dashboard.php?wc=portfolio/category&id=' . $Create->getResult());
        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-price-tags">Adicionar Categoria</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=portfolio/home">Trampos</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=portfolio/categories">Categorias</a>
			<span class="crumb">/</span>
			Nova Categoria
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Ver Categorias!" href="dashboard.php?wc=portfolio/categories" class="btn btn_blue icon-eye">Ver
			todas</a>
		<a title="Nova Categoria" href="dashboard.php?wc=portfolio/category"
		   class="btn btn_add">Adicionar</a>
	</div>

</header>
<div class="dashboard_content">
	<article class="box box100">
		<header>
			<h1>Cadastrar Categoria de Trabalho:</h1>
		</header>
		<div class="box_content">
			<form class="auto_save" name="add" action="" method="post" enctype="multipart/form-data">
				<div class="callback_return"></div>
				<input type="hidden" name="callback" value="Works"/>
				<input type="hidden" name="callback_action" value="add"/>
				<input type="hidden" name="id" value="<?= $catId; ?>"/>

				<label class="label">
					<span class="legend">Nome:</span>
					<input style="font-size: 1.5em;" type="text" name="title" value="<?= $title; ?>"
					       placeholder="Título da Categoria:" required/>
				</label>

				<label class="label">
					<span class="legend">Descrição:</span>
					<textarea style="font-size: 1.2em;" name="content" rows="3"
					          placeholder="Sobre a Categoria:" required><?= $content; ?></textarea>
				</label>

				<label class="label">
					<span class="legend">Seção:</span>
					<select name="parent">
						<option value="">Essa é uma Seção!</option>
                        <?php
                            $Read->fullRead(
                                "SELECT id, title FROM " . DB_PORTFOLIO_CATEGORIES . " WHERE parent IS NULL AND id != :ci ORDER BY title ASC",
                                "ci={$catId}"
                            );
                            if ($Read->getResult()) {
                                foreach ($Read->getResult() as $Sess) {
                                    echo "<option";
                                    if ($Sess['id'] == $parent) {
                                        echo " selected='selected'";
                                    }
                                    echo " value='{$Sess['id']}'>&raquo;{$Sess['title']}</option>";
                                }
                            }
                        ?>
					</select>
				</label>

				<div class='wc_actions flex-end'>
					<button name="public" value="1" class="btn btn_save">
						<img class='form_load' alt='Enviando Requisição!' src='_img/load_w.gif'/>Salvar
					</button>
				</div>

			</form>
		</div>
	</article>
</div>
