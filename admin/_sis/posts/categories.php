<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_POSTS;
    if (!APP_POSTS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    // AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(
            DB_CATEGORIES,
            'WHERE category_title IS NULL AND category_content IS NULL AND category_id >= :st',
            'st=1'
        );
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
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=posts/home">Posts</a>
			<span class="crumb">/</span>
			Categorias
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Nova Categoria" href="dashboard.php?wc=posts/category" class="btn btn_green icon-plus">Adicionar
			Categoria!</a>
	</div>

</header>
<div class="dashboard_content">

    <?php
        $Read->exeRead(DB_CATEGORIES, 'WHERE category_parent IS NULL ORDER BY category_title');
        if (!$Read->getResult()) {
            echo Check::erro(
                \sprintf(
                    '<span>Ainda não existem categorias cadastradas %s. Comece agora mesmo criando sua primeira seção e então suas categorias!</span>',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Sess) {
                echo "<article class='single_category box box100' id='{$Sess['category_id']}'>
                    <header>
                        <h1 class='icon-price-tags'>{$Sess['category_title']}:</h1>
                        <p class='tagline'>" . Check::Words($Sess['category_content'], 60) . "</p>
                        <div class='single_category_actions'>
                            <a target='_blank' title='Ver Categoria!' href='" . BASE . "/artigos/{$Sess['category_name']}' class='btn btn_green icon-eye icon-notext'></a>
                            <a title='Editar Categoria!' href='dashboard.php?wc=posts/category&id={$Sess['category_id']}' class='btn btn_blue icon-pencil icon-notext'></a>
                            <span rel='single_category' class='j_delete_action btn btn_red icon-cancel-circle icon-notext' id='{$Sess['category_id']}'></span>
                            <span rel='single_category' callback='Posts' callback_action='category_remove' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none;' id='{$Sess['category_id']}'>Deletar Categoria?</span>
                        </div>
                    </header>";

                $Read->exeRead(
                    DB_CATEGORIES,
                    'WHERE category_parent = :cid ORDER BY category_title ASC',
                    'cid=' . $Sess['category_id']
                );
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $Cat) {
                        echo "<article class='box_content single_category_sub' id='{$Cat['category_id']}'>
                            <h1 class='icon-price-tag'>{$Cat['category_title']}</h1>
                            <p class='tagline'>" . Check::Words($Cat['category_content'], 60) . "</p>
                            <div class='single_category_actions'>
                                <a target='_blank' title='Ver Categoria!' href='" . BASE . "/artigos/{$Cat['category_name']}' class='btn btn_green icon-eye icon-notext'></a>
                                <a title='Editar Categoria!' href='dashboard.php?wc=posts/category&id={$Cat['category_id']}' class='btn btn_blue icon-pencil icon-notext'></a>
                                <span rel='single_category_sub' class='j_delete_action btn btn_red icon-cancel-circle icon-notext' id='{$Cat['category_id']}'></span>
                                <span rel='single_category_sub' callback='Posts' callback_action='category_remove' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none;' id='{$Cat['category_id']}'>Deletar Categoria?</span>
                            </div>
                        </article>";
                    }
                }
                echo '</article>';
            }
        }
    ?>
</div>
