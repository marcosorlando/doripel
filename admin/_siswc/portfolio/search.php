<?php

    use App\Conn\Read;
    use App\Models\Pager;
    use App\Helpers\Check;

    $adminLevel = 6;
    if (!APP_PORTFOLIO || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $adminLevel) {
        Check::accessBlocked();
    }

    $search = filter_input_array(INPUT_POST);
    if ($search && $search['s']) {
        $s = urlencode($search['s']);
        header("Location: dashboard.php?wc=portfolio/search&s={$s}");
    }

    $getSearch = filter_input(INPUT_GET, 's', FILTER_DEFAULT);
    $thisSearch = urldecode($getSearch);
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-search">Pesquisar Trampos:</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=portfolio/home">Trampos</a>
			<span class="crumb">/</span>
			Pesquisa
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchPosts" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" name="s" value="<?= htmlspecialchars($thisSearch); ?>" placeholder="Pesquisar Trampo:"
			       required/>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>

</header>
<div class="dashboard_content">
    <?php
        $getPage = filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
        $Page = ($getPage ? $getPage : 1);
        $Paginator = new Pager("dashboard.php?wc=portfolio/search&s={$getSearch}&pg=", '<<', '>>', 5);
        $Paginator->exePager($Page, 12);

        $Read ??= new Read();
        $Read->exeRead(
            DB_PORTFOLIO,
            "WHERE title LIKE '%' :s '%' OR skills LIKE '%' :s '%' OR client LIKE '%' :s '%' ORDER BY status ASC, delivery DESC LIMIT :limit OFFSET :offset",
            "s={$thisSearch}&limit={$Paginator->getLimit()}&offset={$Paginator->getOffset()}"
        );
        if (!$Read->getResult()) {
            $Paginator->returnPage();
            echo Check::ajaxErro(
                "<span class='al_center icon-notification'>Olá {$Admin['user_name']}. Sua pesquisa para {$thisSearch} não obteve resultados. Você pode tentar outros termos!</span>",
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $MAT) {
                extract($MAT);

                $PostCover = (file_exists("../uploads/portfolio/{$img_970x500}") && !is_dir(
                    "../uploads/portfolio/{$img_970x500}"
                ) ? "uploads/portfolio/{$img_970x500}" : 'admin/_img/no_image.jpg');
                $PostStatus = ($status == 1 ? '<span class="btn btn_green icon-checkmark icon-notext"></span>' : '<span class="btn btn_yellow icon-warning icon-notext"></span>');
                $title = (!empty($title) ? $title : 'Edite esse rascunho para poder exibir como trabalho em seu site!');

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
                    <img alt='{$title}' title='{$title}' src='../tim.php?src={$PostCover}&w=" . IMAGE_W . "&h=" . IMAGE_H . "'/>
                    <div class='post_single_status'><span class='btn'>" . str_pad($views, 4, 0, STR_PAD_LEFT) . "</span>{$PostStatus}</div>
                    <div class='post_single_cat'>{$Category}</div>
                </div>
                <div class='box_content'>
                    <h1 class='title'>" . Check::Chars($title, 56) . "</h1>
                    <a title='Ver trampo no site' target='_blank' href='" . BASE . "/trampo/{$name}' class='icon-notext icon-eye btn btn_green'></a>
                    <a title='Editar Trampo href='dashboard.php?wc=portfolio/create&id={$id}' class='post_single_center icon-notext icon-pencil btn btn_blue'></a>
                    <span rel='post_single' class='j_delete_action icon-notext icon-cancel-circle btn btn_red' id='{$id}'></span>
                    <span rel='post_single' callback='Works' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$id}'>Deletar Trampo?</span>
                </div>
            </article>";
            }

            $Paginator->exePaginator(
                DB_PORTFOLIO,
                "WHERE title LIKE '%' :s '%' OR skills LIKE '%' :s '%' OR client LIKE '%' :s '%'",
                "s={$thisSearch}"
            );
            echo $Paginator->getPaginator();
        }
    ?>
</div>
