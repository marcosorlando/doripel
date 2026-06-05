<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = 6;
    if (!APP_MATERIALS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    $Read = new Read();

    // AUTO DELETE CATEGORIA DE MATERIAIS TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(
            DB_MATCATEGORIES,
            'WHERE category_title IS NULL AND category_content IS NULL AND category_id >= :st',
            'st=1'
        );
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-price-tags">Categorias de Materiais</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=materiais/home">Materiais</a>
			<span class="crumb">/</span>
			Categorias
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Nova Categoria" href="dashboard.php?wc=materiais/category" class="btn btn_green icon-plus">Adicionar
			Categoria!</a>
	</div>

</header>
<div class="dashboard_content">

    <?php
        $Read->exeRead(DB_MATCATEGORIES, 'WHERE category_parent IS NULL ORDER BY category_title ASC');
        if (!$Read->getResult()) {
            echo Check::erro(
                sprintf(
                    '<span>Ainda não existem categorias cadastradas %s. Comece agora mesmo criando sua primera sessão e então suas categorias!</span>',
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
                            
                            <a title='Editar Categoria!' href='dashboard.php?wc=materiais/category&id={$Sess['category_id']}' class='btn btn_blue icon-pencil icon-notext'></a>
                            <span rel='single_category' class='j_delete_action btn btn_red icon-cancel-circle icon-notext' id='{$Sess['category_id']}'></span>
                            <span rel='single_category' callback='Mats' callback_action='category_remove' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none;' id='{$Sess['category_id']}'>Deletar Categoria?</span>
                        </div>
                    </header>";

                $Read->exeRead(
                    DB_MATCATEGORIES,
                    'WHERE category_parent = :cid ORDER BY category_title ASC',
                    'cid=' . $Sess['category_id']
                );
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $Cat) {
                        echo "<article class='box_content single_category_sub' id='{$Cat['category_id']}'>
                            <h1 class='icon-price-tag'>{$Cat['category_title']}</h1>
                            <p class='tagline'>" . Check::Words($Cat['category_content'], 60) . "</p>
                            <div class='single_category_actions'>
                                
                                <a title='Editar Categoria!' href='dashboard.php?wc=materiais/category&id={$Cat['category_id']}' class='btn btn_blue icon-pencil icon-notext'></a>
                                <span rel='single_category_sub' class='j_delete_action btn btn_red icon-cancel-circle icon-notext' id='{$Cat['category_id']}'></span>
                                <span rel='single_category_sub' callback='Mats' callback_action='category_remove' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none;' id='{$Cat['category_id']}'>Deletar Categoria?</span>
                            </div>
                        </article>";
                    }
                }
                echo '</article>';
            }
        }
    ?>
</div>
