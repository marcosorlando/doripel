<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;

    if (!APP_PORTFOLIO || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < LEVEL_WC_TRAMPOS) {
        Check::accessBlocked();
    }

    $Read ??= new Read();

    //AUTO DELETE CATEGORIA DE MATERIAIS TRASH
    if (DB_AUTO_TRASH) {
        $Delete ??= new Delete();
        $Delete->exeDelete(
            DB_PORTFOLIO_CATEGORIES,
            "WHERE title IS NULL AND content IS NULL AND id >= :st",
            "st=1"
        );
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-price-tags">Categorias de Trampos</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=portfolio/home">Trampos</a>
			<span class="crumb">/</span>
			Categorias
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Nova Categoria" href="dashboard.php?wc=portfolio/category" class="btn btn_green icon-plus">Adicionar
			Categoria!</a>
	</div>

</header>
<div class="dashboard_content">

    <?php
        $Read->exeRead(DB_PORTFOLIO_CATEGORIES, "WHERE parent IS NULL ORDER BY title ASC");
        if (!$Read->getResult()) {
            echo Check::ajaxErro(
                "Ainda não existem categorias cadastradas. Comece agora mesmo criando sua primeira sessão e então suas categorias!",
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Sess) {
                echo "<article class='single_category box box100' id='{$Sess['id']}'>
                    <header>
                        <h1 class='icon-price-tags'>{$Sess['title']}:</h1>
                        <p class='tagline'>" . Check::Words($Sess['content'], 60) . "</p>
                        <div class='single_work_actions'>
                            <a target='_blank' title='Ver Categoria!' href='" . BASE . "/artigos/{$Sess['name']}' class='btn btn_green icon-eye icon-notext'></a>
                            <a title='Editar Categoria!' href='dashboard.php?wc=portfolio/category&id={$Sess['id']}' class='btn btn_blue icon-pencil icon-notext'></a>
                            <span rel='single_category' class='j_delete_action btn btn_red icon-cancel-circle icon-notext' id='{$Sess['id']}'></span>
                            <span rel='single_category' callback='Works' callback_action='remove' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none;' id='{$Sess['id']}'>Deletar Categoria?</span>
                        </div>
                    </header>";

                $Read->exeRead(
                    DB_PORTFOLIO_CATEGORIES,
                    "WHERE parent = :cid ORDER BY title ASC",
                    "cid={$Sess['id']}"
                );
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $Cat) {
                        echo "<article class='box_content single_work_sub' id='{$Cat['id']}'>
                            <h1 class='icon-price-tag'>{$Cat['title']}</h1>
                            <p class='tagline'>" . Check::Words($Cat['content'], 60) . "</p>
                            <div class='single_work_actions'>
                                <a target='_blank' title='Ver Categoria!' href='" . BASE . "/portfolio/{$Cat['name']}' class='btn btn_green icon-eye icon-notext'></a>
                                <a title='Editar Categoria!' href='dashboard.php?wc=portfolio/category&id={$Cat['id']}' class='btn btn_blue icon-pencil icon-notext'></a>
                                <span rel='single_work_sub' class='j_delete_action btn btn_red icon-cancel-circle icon-notext' id='{$Cat['id']}'></span>
                                <span rel='single_work_sub' callback='Works' callback_action='remove' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none;' id='{$Cat['id']}'>Deletar Categoria?</span>
                            </div>
                        </article>";
                    }
                }
                echo "</article>";
            }
        }
    ?>
</div>
