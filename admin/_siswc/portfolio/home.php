<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Models\Pager;
    use App\Helpers\Check;

    $adminLevel = 6;
    if (!APP_PORTFOLIO || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $adminLevel) {
        Check::accessBlocked();
    }

    //AUTO DELETE MATERIAL TRASH
    if (DB_AUTO_TRASH) {
        $Delete ??= new Delete();
        $Delete->exeDelete(
            DB_PORTFOLIO,
            "WHERE title IS NULL AND category IS NULL and status = :st",
            "st=0"
        );
    }

    $Read ??= new Read();
    $search = filter_input_array(INPUT_POST);
    if ($search && $search['s']) {
        $s = urlencode($search['s']);
        header("Location: dashboard.php?wc=portfolio/search&s={$s}");
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon icon-wrench">Trampos Realizados</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Portfólio
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchPosts" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" name="s" placeholder="Pesquisar Trampo:" required/>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>
</header>
<div class="dashboard_content">
    <?php
        $getPage = filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
        $Page = ($getPage ?: 1);
        $Paginator = new Pager('dashboard.php?wc=portfolio/home&pg=', '<<', '>>', 5);
        $Paginator->exePager($Page, 12);

        $Read->exeRead(
            DB_PORTFOLIO,
            "ORDER BY status ASC, deliveryted_at DESC LIMIT :limit OFFSET :offset",
            "limit={$Paginator->getLimit()}&offset={$Paginator->getOffset()}"
        );
        if (!$Read->getResult()) {
            $Paginator->returnPage();
            echo Check::ajaxErro(
                "Ainda não existem trabalhos cadastrados. Comece agora mesmo cadastrando seu primeiro trabalho!",
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $POST) {
                extract($POST);

                $PostCover = (file_exists("../uploads/portfolio/{$img_970x500}") && !is_dir(
                    "../uploads/portfolio/{$img_970x500}"
                ) ? "uploads/portfolio/{$img_970x500}" : 'admin/_img/no_image.jpg');
                $PostStatus = ($status == 1 && strtotime($deliveryted_at) >= strtotime(
                    date('Y-m-d H:i:s')
                ) ? '<span class="btn btn_blue icon-clock icon-notext"></span>' : ($status == 1 ? '<span class="btn btn_green icon-checkmark icon-notext"></span>' : '<span class="btn btn_yellow icon-warning icon-notext"></span>'));
                $title = (!empty($title) ? $title : 'Edite esse rascunho para poder exibir como artigo em seu site!');

                $Category = null;
                if (!empty($category)) {
                    $Read->fullRead(
                        "SELECT title FROM " . DB_PORTFOLIO_CATEGORIES . " WHERE id = :ct",
                        "ct={$category}"
                    );
                    if ($Read->getResult()) {
                        $Category = "<span class='icon-price-tags'>{$Read->getResult()[0]['title']}</span> ";
                    }
                }

                if (!empty($parent)) {
                    $Read->fullRead(
                        "SELECT title FROM " . DB_PORTFOLIO_CATEGORIES . " WHERE id IN({$parent})"
                    );
                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $SubCat) {
                            $Category .= "<span class='icon-price-tag'>{$SubCat['title']}</span> ";
                        }
                    }
                }

                echo "<article class='box box25 post_single' id='{$id}'>           
                <div class='post_single_cover'>
                    <img alt='{$title}' title='{$title}' src='../tim.php?src={$PostCover}&w=1200&h=628'/>
                    <div class='post_single_status'><span class='btn'>" . str_pad(
                        $views ?? '',
                        4,
                        0,
                        STR_PAD_LEFT
                    ) .
                    "</span>{$PostStatus}</div>
                    <div class='post_single_cat'>{$Category}</div>
                </div>
                <div class='box_content'>
                    <h1 class='title'>" . Check::Chars($title, 35) . "</h1>
                    <a title='Ver trampo no site' target='_blank' href='" . BASE . "/trampo/{$slug}' class='icon-notext icon-eye btn btn_green'></a>
                    <a title='Editar Trampo' href='dashboard.php?wc=portfolio/create&id={$id}' class='post_single_center icon-notext icon-pencil btn btn_blue'></a>
                    <span rel='post_single' class='j_delete_action icon-notext icon-cancel-circle btn btn_red' id='{$id}'></span>
                    <span rel='post_single' callback='Works' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$id}'>Deletar Trampo?</span>
                </div>
            </article>";
            }

            $Paginator->exePaginator(DB_PORTFOLIO);
            echo $Paginator->getPaginator();
        }
    ?>
</div>
