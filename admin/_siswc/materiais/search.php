<?php

    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = 6;
    if (!APP_MATERIALS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    $Search = filter_input_array(INPUT_POST);
    if ($Search && $Search['s']) {
        $S = Check::safeUrlEncode($Search['s']);
        header('Location: dashboard.php?wc=materiais/search&s=' . $S);
    }

    $GetSearch = filter_input(INPUT_GET, 's', FILTER_DEFAULT);
    $ThisSearch = Check::safeUrlEncode($GetSearch);
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-search">Pesquisar Materiais:</h1>
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
			Pesquisa
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchPosts" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" name="s" value="<?php
                echo Check::safeHtmlChars($ThisSearch); ?>"
			       placeholder="Pesquisar Material:" required/>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>

</header>
<div class="dashboard_content">
    <?php
        $getPage = filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
        $Page = $getPage ?? 1;
        $Paginator = new Pager(sprintf('dashboard.php?wc=materiais/search&s=%s&pg=', $GetSearch), '<<', '>>', 5);
        $Paginator->exePager($Page, 12);

        $Read = new Read();
        $Read->exeRead(
            DB_MATERIAIS,
            "WHERE mat_title LIKE '%' :s '%' OR mat_subtitle LIKE '%' :s '%' ORDER BY mat_status ASC, mat_date DESC LIMIT :limit OFFSET :offset",
            sprintf('s=%s&limit=%d&offset=%d', $ThisSearch, $Paginator->getLimit(), $Paginator->getOffset())
        );
        if (!$Read->getResult()) {
            $Paginator->returnPage();
            echo Check::erro(
                sprintf(
                    "<span class='al_center icon-notification'>Olá %s. Sua pesquisa para %s não obteve resultados. Você pode tentar outros termos!</span>",
                    $Admin['user_name'],
                    $ThisSearch
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $MAT) {
                extract($MAT);

                $PostCover = (file_exists('../uploads/' . $mat_cover) && !is_dir(
                    '../uploads/' . $mat_cover
                ) ? 'uploads/' . $mat_cover : 'admin/_img/no_image.jpg');
                $PostStatus = (1 == $mat_status ? '<span class="btn btn_green icon-checkmark icon-notext"></span>' : '<span class="btn btn_yellow icon-warning icon-notext"></span>');
                $mat_title = (empty($mat_title) ? 'Edite esse rascunho para poder exibir como material em seu site!' : $mat_title);

                $Category = null;
                if (!empty($mat_category)) {
                    $Read->fullRead(
                        'SELECT category_title FROM ' . DB_MATCATEGORIES . ' WHERE category_id = :ct',
                        'ct=' . $mat_category
                    );
                    if ($Read->getResult()) {
                        $Category = sprintf(
                            "<span class='icon-price-tags'>%s</span> ",
                            $Read->getResult()[0]['category_title']
                        );
                    }
                }

                if (!empty($mat_category_parent)) {
                    $Read->fullRead(
                        'SELECT category_title FROM ' . DB_MATCATEGORIES . sprintf(
                            ' WHERE category_id IN(%s)',
                            $mat_category_parent
                        )
                    );
                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $SubCat) {
                            $Category .= sprintf("<span class='icon-price-tag'>%s</span> ", $SubCat['category_title']);
                        }
                    }
                }

                echo "<article class='box box25 post_single' id='{$mat_id}'>
                <div class='post_single_cover'>
                    <img alt='{$mat_title}' title='{$mat_title}' src='../tim.php?src={$PostCover}&w=" . IMAGE_W . '&h=' . IMAGE_H . "'/>
                    <div class='post_single_status'><span class='btn'>" . str_pad(
                        (string)$mat_views,
                        4,
                        0,
                        STR_PAD_LEFT
                    ) . "</span>{$PostStatus}</div>
                    <div class='post_single_cat'>{$Category}</div>
                </div>
                <div class='box_content'>
                    <h1 class='title'>" . Check::chars($mat_title, 56) . "</h1>
                    <a title='Ver material no site' target='_blank' href='" . BASE . "/materiais#{$mat_name}' class='icon-notext icon-eye btn btn_green'></a>
                    <a title='Editar Material' href='dashboard.php?wc=materiais/create&id={$mat_id}' class='post_single_center icon-notext icon-pencil btn btn_blue'></a>
                    <span rel='post_single' class='j_delete_action icon-notext icon-cancel-circle btn btn_red' id='{$mat_id}'></span>
                    <span rel='post_single' callback='Mats' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$mat_id}'>Deletar Material?</span>
                </div>
            </article>";
            }

            $Paginator->exePaginator(
                DB_MATERIAIS,
                "WHERE mat_title LIKE '%' :s '%' OR mat_subtitle LIKE '%' :s '%'",
                's=' . $ThisSearch
            );
            echo $Paginator->getPaginator();
        }
    ?>
</div>
