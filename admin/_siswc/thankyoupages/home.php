<?php

use App\Conn\Delete;
use App\Conn\Read;
use App\Helpers\Check;
use App\Models\Pager;

$AdminLevel = LEVEL_WC_THANKYOU_PAGES;
if (!APP_THANKYOU_PAGES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
    exit('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não está logado <br>ou não tem permissão para acessar essa página!</div>');
}

// AUTO INSTANCE OBJECT READ
$Read ??= new Read();

// AUTO DELETE POST TRASH
if (DB_AUTO_TRASH !== 0) {
    $Delete = new Delete();

    // AUTO TRASH IMAGES
    $Read->fullRead(
        'SELECT page_cover, page_logo FROM ' . DB_THANKYOU_PAGES . ' WHERE page_title IS NULL AND page_status = :st',
        'st=0'
    );

    if ($Read->getResult()) {
        foreach ($Read->getResult() as $PageImage) {
            $CoverRemove = '../../uploads/thankyoupages/' . $PageImage['page_cover'];
            $LogoRemove = '../../uploads/thankyoupages/' . $PageImage['page_logo'];

            if (file_exists($CoverRemove) && !is_dir($CoverRemove)) {
                unlink($CoverRemove);
            }
            if (file_exists($LogoRemove) && !is_dir($LogoRemove)) {
                unlink($LogoRemove);
            }
        }
    }
    $Delete->exeDelete(DB_THANKYOU_PAGES, 'WHERE page_title IS NULL AND page_status = :st', 'st=0');
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
			Thank You Page
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Nova Página de Cliente" href="dashboard.php?wc=thankyoupages/create" class="btn btn_green icon-plus">Nova
			Thank You Page</a>
	</div>

</header>
<div class="dashboard_content">
    <?php
    $getPage = filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
    $Page = $getPage ?? 1;
    $Paginator = new Pager('dashboard.php?wc=thankyoupages/home&pg=', '<<', '>>', 10);
    $Paginator->exePager($Page, 12);

    $Read->exeRead(
        DB_THANKYOU_PAGES,
        'ORDER BY page_title ASC, page_date DESC LIMIT :limit OFFSET :offset',
        sprintf('limit=%d&offset=%d', $Paginator->getLimit(), $Paginator->getOffset())
    );
    if (!$Read->getResult()) {
        $Paginator->returnPage();
        echo Check::erro(
            sprintf(
                '<span>Ainda não existem páginas cadastradas %s. Comece agora mesmo criando sua primeira página de cliente!</span>',
                $Admin['user_name']
            ),
            E_USER_NOTICE
        );
    } else {
        foreach ($Read->getResult() as $PAGE) {
            extract($PAGE);
            $page_status = (1 == $page_status ? '<span class="icon-checkmark font_green">Publicada</span>' : '<span class="icon-warning font_yellow">Rascunho</span>');
            $page_cover = (empty($page_cover) ? '' : BASE . sprintf(
                    '/tim.php?src=uploads/thankyoupages/%s&w=',
                    $page_cover
                ) . IMAGE_W / 2 . '&h=' . IMAGE_H / 2 . '');
            $page_logo = ('' === $page_cover || '0' === $page_cover ? '' : BASE . sprintf(
                    '/tim.php?src=uploads/thankyoupages/%s&w=',
                    $page_logo
                ) . IMAGE_W / 3 . '&h=' . IMAGE_H / 3 . '');

            echo "<article class='box box25 page_single wc_draganddrop' callback='Thankyoupages' id='{$page_id}'>

                <a title='Ver página no site' target='_blank' href='" . BASE . "/{$page_name}'><img alt='{$page_title}' src='{$page_cover}'/></a>
                <div class='box_content wc_normalize_height'>
                    <h1 class='title'><a title='Ver página no site' target='_blank' href='" . BASE . "/{$page_name}'>/{$page_title}</a></h1>
                    <p>{$page_status}</p>
                </div>
                <div class='page_single_action'>
                    <a title='Editar Página' href='dashboard.php?wc=thankyoupages/create&id={$page_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                    <span rel='page_single' class='j_delete_action icon-cancel-circle btn btn_red' id='{$page_id}'>Excluir</span>
                    <span rel='page_single' callback='Thankyoupages' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$page_id}'>Deletar Página?</span>
                </div>
            </article>";
        }

        $Paginator->exePaginator(DB_THANKYOU_PAGES);
        echo $Paginator->getPaginator();
    }
    ?>
</div>
