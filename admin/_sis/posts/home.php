<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_POSTS;
    if (!APP_POSTS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }
    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    // AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(DB_POSTS, 'WHERE post_title IS NULL AND post_content IS NULL and post_status = :st', 'st=0');

        // AUTO TRASH IMAGES
        $Read->fullRead(
            'SELECT image FROM ' . DB_POSTS_IMAGE . ' WHERE post_id NOT IN(SELECT post_id FROM ' . DB_POSTS . ')'
        );
        if ($Read->getResult()) {
            $Delete->exeDelete(
                DB_POSTS_IMAGE,
                'WHERE id >= :id AND post_id NOT IN(SELECT post_id FROM ' . DB_POSTS . ')',
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

    $S = \filter_input(INPUT_GET, 's');
    $C = \filter_input(INPUT_GET, 'cat');
    $T = \filter_input(INPUT_GET, 'tag');

    $Search = \filter_input_array(INPUT_POST);
    if ($Search && (isset($Search['s']) || isset($Search['status']))) {
        $S = (isset($Search['s']) ? Check::safeUrlEncode($Search['s']) : $S);
        $SearchCat = (empty($Search['searchcat']) ? null : $Search['searchcat']);
        \header(\sprintf('Location: dashboard.php?wc=posts/home&s=%s&cat=%s&tag=%s', $S, $SearchCat, $T));

        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-blog">Posts<?php
                echo $C ? ' por Categoria' : ''; ?><?php
                echo $T ? ' em ' . $T : ''; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="Todos os Posts" href="dashboard.php?wc=posts/home">Posts</a>
            <?php
                echo $S ? \sprintf("<span class='crumb'>/</span> <span class='icon-search'>%s</span>", $S) : ''; ?>
		</p>
	</div>

	<div class="dashboard_header_search">

		<form name="searchCategoriesPost" action="" method="post"
		      enctype="multipart/form-data" class="ajax_off">

			<div class="flex">
				<input type='search' value="<?php
                    echo $S; ?>" name='s' placeholder='Pesquisar:'
				>
				<select name='searchcat'>
					<option value=''>Todos</option>
                    <?php
                        $Read->fullRead(
                            'SELECT category_id, category_title FROM ' . DB_CATEGORIES . ' WHERE category_parent IS NULL ORDER BY category_title ASC'
                        );
                        if (!$Read->getResult()) {
                            echo "<option value='' disabled='disabled'>Não existem categorias cadastradas!</option>";
                        } else {
                            foreach ($Read->getResult() as $SearchCategory) {
                                echo '<option ' . ($C == $SearchCategory['category_id'] ? "selected='selected'" : null) . \sprintf(
                                        " value='%s'>%s</option>",
                                        $SearchCategory['category_id'],
                                        $SearchCategory['category_title']
                                    );

                                $Read->fullRead(
                                    'SELECT category_id, category_title FROM ' . DB_CATEGORIES . \sprintf(
                                        ' WHERE category_parent = %s ORDER BY category_title ASC',
                                        $SearchCategory['category_id']
                                    )
                                );
                                if ($Read->getResult()) {
                                    foreach ($Read->getResult() as $SearchCategorySub) {
                                        echo '<option ' . ($C == $SearchCategorySub['category_id'] ? "selected='selected'" : null) . \sprintf(
                                                " value='%s'> &raquo; %s</option>",
                                                $SearchCategorySub['category_id'],
                                                $SearchCategorySub['category_title']
                                            );
                                    }
                                }
                            }
                        }
                    ?>
				</select>
				<button class="btn btn_green icon icon-search icon-notext"></button>
			</div>

		</form>
	</div>
</header>

<div class="dashboard_content">
    <?php
        $getPage = \filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
        $Page = ($getPage ?? 1);
        $Paginator = new Pager(
            \sprintf('dashboard.php?wc=posts/home&s=%s&cat=%s&tag=%s&pg=', $S, $C, $T)
        );
        $Paginator->exePager($Page, 12);

        if (!empty($C)) {
            $WhereCat[0] = "AND ((post_category = :cat OR FIND_IN_SET(:cat, post_category_parent)) OR :cat = '')";
            $WhereCat[1] = '&cat=' . $C;
        } else {
            $WhereCat[0] = '';
            $WhereCat[1] = '';
        }

        if (!empty($T)) {
            $WhereTag[0] = "AND post_tags LIKE '%' :tag '%'";
            $WhereTag[1] = '&tag=' . $T;
        } else {
            $WhereTag[0] = '';
            $WhereTag[1] = '';
        }

        if (!empty($S)) {
            $WhereString[0] = "AND (post_title LIKE '%' :s '%' OR post_content LIKE '%' :s '%')";
            $WhereString[1] = '&s=' . $S;
        } else {
            $WhereString[0] = '';
            $WhereString[1] = '';
        }

        $Read->fullRead(
            'SELECT * FROM ' . DB_POSTS . ' WHERE 1=1 '
            . ($WhereCat[0] . ' ')
            . ($WhereTag[0] . ' ')
            . ($WhereString[0] . ' ')
            . 'ORDER BY post_status ASC, post_date DESC '
            . 'LIMIT :limit OFFSET :offset',
            \sprintf(
                'limit=%d&offset=%d%s%s%s',
                $Paginator->getLimit(),
                $Paginator->getOffset(),
                $WhereCat[1],
                $WhereTag[1],
                $WhereString[1]
            )
        );

        if (!$Read->getResult()) {
            $Paginator->returnPage();
            echo Check::erro(
                \sprintf(
                    "Ainda não existem posts cadastrados %s. Comece agora mesmo criando seu primeiro post!",
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $POST) {
                \extract($POST);

                $PostCover = (\file_exists('../uploads/' . $post_cover) && !\is_dir(
                    '../uploads/' . $post_cover
                ) ? 'uploads/' . $post_cover : 'admin/_img/no_image.jpg');
                $PostStatus = (1 == $post_status && \strtotime((string)$post_date) >= \strtotime(
                    \date('Y-m-d H:i:s')
                ) ? '<span class="btn btn_blue icon-clock icon-notext wc_tooltip"><span class="wc_tooltip_baloon">Agendado</span></span>' : (1 == $post_status ? '<span class="btn btn_green icon-checkmark icon-notext wc_tooltip"><span class="wc_tooltip_baloon">Publicado</span></span>' : '<span class="btn btn_yellow icon-warning icon-notext wc_tooltip"><span class="wc_tooltip_baloon">Pendente</span></span>'));
                $post_title = (empty($post_title) ? 'Edite esse rascunho para poder exibir como artigo em seu site!' : $post_title);

                $postTags = null;
                /*   if ($post_tags) {
                       foreach (\explode(',', (string)$post_tags) as $tags) {
                           $tag = \ltrim(\rtrim($tags));
                           $postTags .= \sprintf(
                                   "<a class='icon-price-tag radius' title='Artigos marcados com %s' href='dashboard.php?wc=posts/home&s=%s&cat=%s&tag=",
                                   $tag,
                                   $S,
                                   $C
                               ) . Check::safeUrlEncode($tag) . \sprintf("'>%s</a>", $tag);
                       }
                   }*/

                $Category = null;
                if (!empty($post_category)) {
                    $Read->fullRead(
                        'SELECT category_id, category_title FROM ' . DB_CATEGORIES . ' WHERE category_id = :ct',
                        'ct=' . $post_category
                    );
                    if ($Read->getResult()) {
                        $Category = \sprintf(
                                "<span class='icon-bookmark'><a title='Artigos em %s' href='dashboard.php?wc=posts/home&s=%s&cat=%s&tag=",
                                $Read->getResult()[0]['category_title'],
                                $S,
                                $Read->getResult()[0]['category_id']
                            ) . Check::safeUrlEncode($T) . \sprintf(
                                "'>%s</a></span> ",
                                $Read->getResult()[0]['category_title']
                            );
                    }
                }

                if (!empty($post_category_parent)) {
                    $Read->fullRead(
                        'SELECT category_title, category_id FROM ' . DB_CATEGORIES . \sprintf(
                            ' WHERE category_id IN(%s)',
                            $post_category_parent
                        )
                    );
                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $SubCat) {
                            $Category .= \sprintf(
                                    "<span class='icon-bookmarks'><a title='Artigos em %s' href='dashboard.php?wc=posts/home&s=%s&cat=%s&tag=",
                                    $SubCat['category_title'],
                                    $S,
                                    $SubCat['category_id']
                                ) . Check::safeUrlEncode($T) . \sprintf("'>%s</a></span> ", $SubCat['category_title']);
                        }
                    }
                }

                echo "<article class='box box25 post_single' id='{$post_id}'>           
                <div class='post_single_cover'>
                    <a title='Ver artigo no site' target='_blank' href='" . BASE . \sprintf(
                        "/artigo/%s'><img alt='%s' title='%s' src='../tim.php?src=%s&w=",
                        $post_name,
                        $post_title,
                        $post_title,
                        $PostCover
                    ) . IMAGE_W / 2 . '&h=' . IMAGE_H / 2 . "'/></a>
                    <div class='post_single_status'>
                        <span class='btn wc_tooltip'>" . \str_pad((string)$post_views, 4, 0, STR_PAD_LEFT) . " <span class='wc_tooltip_baloon'>Visualizações</span></span>
                        {$PostStatus}
                        " . (APP_POSTS_INSTANT_ARTICLE && '1' == $post_instant_article ? "<span class='btn btn_blue icon-facebook2 icon-notext wc_tooltip'><span class='wc_tooltip_baloon'>Instant Article</span></span>" : '') . ' 
                    </div>
                    ' . (null === $postTags || '' === $postTags || '0' === $postTags ? '' : \sprintf(
                        "<div class='post_single_tag'>%s</div>",
                        $postTags
                    )) . "
                </div>
                <div class='post_single_content wc_normalize_height'>
                    <h1 class='title'><a title='Ver artigo no site' target='_blank' href='" . BASE . "/artigo/{$post_name}'>{$post_title}</a></h1>
                    <p class='post_single_cat'>{$Category}</p>
                </div>
                <div class='post_single_actions'>
                    <a title='Editar Artigo' href='dashboard.php?wc=posts/create&id={$post_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                    <span rel='post_single' class='j_delete_action icon-cancel-circle btn btn_red' id='{$post_id}'>Deletar</span>
                    <span rel='post_single' callback='Posts' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$post_id}'>Deletar Post?</span>
                </div>
            </article>";
            }

            $Paginator->exePaginator(
                DB_POSTS,
                'WHERE '
                . "((post_category = :cat OR FIND_IN_SET(:cat, post_category_parent)) OR :cat = '') "
                . "AND (FIND_IN_SET(:tag, post_tags) OR :tag = '') "
                . "AND (post_title LIKE '%' :s '%' OR post_content LIKE '%' :s '%')",
                \sprintf('cat=%s&tag=%s&s=%s', $C, $T, $S)
            );
            echo $Paginator->getPaginator();
        }
    ?>
</div>
