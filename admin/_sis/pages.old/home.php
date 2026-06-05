<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_PAGES;
    if (!APP_PAGES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    // AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(DB_PAGES, 'WHERE page_title IS NULL AND page_content IS NULL AND page_status = :st', 'st=0');

        // AUTO TRASH IMAGES
        $Read->fullRead(
            'SELECT image FROM ' . DB_PAGES_IMAGE . ' WHERE page_id NOT IN(SELECT page_id FROM ' . DB_PAGES . ')'
        );
        if ($Read->getResult()) {
            $Delete->exeDelete(
                DB_PAGES_IMAGE,
                'WHERE id >= :id AND page_id NOT IN(SELECT page_id FROM ' . DB_PAGES . ')',
                'id=1'
            );
            foreach ($Read->getResult() as $ImageRemove) {
                if (
                    \file_exists('../uploads/' . $ImageRemove['image']) && !\is_dir(
                        '../uploads/' . $ImageRemove['image']
                    )
                ) {
                    \unlink('../uploads/' . $ImageRemove['image']);
                }
            }
        }
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-pagebreak">Páginas</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Páginas
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Nova Página" href="dashboard.php?wc=pages/create" class="btn btn_green icon-plus">Adicionar Nova
			Página!</a>
		<span class="btn btn_green icon-spinner9 wc_drag_active" title="Organizar Cursos">Ordenar</span>
	</div>

</header>
<div class="dashboard_content">
    <?php
        $getPage = \filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
        $Page = $getPage ?? 1;
        $Paginator = new Pager('dashboard.php?wc=pages/home&pg=', '<<', '>>', 5);
        $Paginator->exePager($Page, 12);

        $Read->exeRead(
            DB_PAGES,
            'ORDER BY page_order ASC, page_title ASC, page_date DESC LIMIT :limit OFFSET :offset',
            \sprintf('limit=%d&offset=%d', $Paginator->getLimit(), $Paginator->getOffset())
        );
        if (!$Read->getResult()) {
            $Paginator->returnPage();
            echo Check::erro(
                \sprintf(
                    "<span class='al_center icon-notification'>Ainda não existem páginas cadastrados %s. Comece agora mesmo criando sua primeira página!</span>",
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $PAGE) {
                \extract($PAGE);
                $page_status = (1 == $page_status ? '<span class="icon-checkmark font_green">Publicada</span>' : '<span class="icon-warning font_yellow">Rascunho</span>');
                $page_cover = (empty($page_cover) ? '' : BASE . \sprintf(
                        '/tim.php?src=uploads/%s&w=',
                        $page_cover
                    ) . IMAGE_W / 4 . '&h=' . IMAGE_H / 4 . '');

                echo "<article class='box box25 page_single wc_draganddrop' callback='Pages' callback_action='pages_order' id='{$page_id}'>
                <a title='Ver página no site' target='_blank' href='" . BASE . "/{$page_name}'><img alt='' title='' src='{$page_cover}'/></a>
                <div class='box_content wc_normalize_height'>
                    <h1 class='title'><a title='Ver página no site' target='_blank' href='" . BASE . "/{$page_name}'>/{$page_title}</a></h1>
                    <p>{$page_status}</p>
                </div>
                <div class='page_single_action'>
                    <a title='Editar Página' href='dashboard.php?wc=pages/create&id={$page_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                    <span rel='page_single' class='j_delete_action icon-cancel-circle btn btn_red' id='{$page_id}'>Excluir</span>
                    <span rel='page_single' callback='Pages' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$page_id}'>Deletar Página?</span>
                </div>
            </article>";
            }

            $Paginator->exePaginator(DB_PAGES);
            echo $Paginator->getPaginator();
        }
    ?>
</div>
