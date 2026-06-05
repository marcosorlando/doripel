<?php

use App\Conn\Create;

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_ALBUMS;
    if (!APP_ALBUMS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO DELETE PRODUCT TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(
            DB_ALBUMS,
            'WHERE album_title IS NULL AND album_subtitle IS NULL and album_status = :st',
            'st=0'
        );

        // AUTO TRASH IMAGES
        $Read->fullRead(
            'SELECT image FROM ' . DB_ALBUMS_IMAGE . ' WHERE album_id NOT IN(SELECT album_id FROM ' . DB_ALBUMS . ')'
        );
        if ($Read->getResult()) {
            $Delete->exeDelete(
                DB_ALBUMS_IMAGE,
                'WHERE id >= :id AND album_id NOT IN(SELECT album_id FROM ' . DB_ALBUMS . ')',
                'id=1'
            );
            foreach ($Read->getResult() as $ImageRemove) {
                if (
                    \file_exists('../uploads/albuns/' . $ImageRemove['image']) && !\is_dir(
                        '../uploads/albuns/' . $ImageRemove['image']
                    )
                ) {
                    \unlink('../uploads/albuns/' . $ImageRemove['image']);
                }
            }
        }
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $S = \filter_input(INPUT_GET, 's', FILTER_DEFAULT);
    $O = \filter_input(INPUT_GET, 'opt', FILTER_DEFAULT);

    $WhereString = (empty($S) ? '' : \sprintf(
        " AND (album_title LIKE '%%%s%%' OR album_subtitle LIKE '%%%s%%') ",
        $S,
        $S
    ));
    $WhereOpt = ((empty($O)) ? '' : ('#' == $O ? ' AND album_status != 1 ' : \sprintf(
        ' AND album_subcategory = %s ',
        $O
    )));

    $Search = \filter_input_array(INPUT_POST);
    if ($Search) {
        $S = \urlencode((string)$Search['s']);
        $O = \urlencode((string)$Search['opt']);
        \header(\sprintf('Location: dashboard.php?wc=albums/home&opt=%s&s=%s', $O, $S));
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-books">Álbuns</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Álbuns
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchPosts" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" name="s" placeholder="Pesquisar:" style="width: 38%; margin-right: 3px;"/>
			<select name="opt" style="width: 45%; margin-right: 3px; padding: 5px 10px">
				<option value="">Todos</option>

                <?php
                    $Read->fullRead(
                        'SELECT presential_cat_id, presential_cat_title FROM ' . DB_PRESENTIAL_CATEGORIES . ' WHERE presential_cat_id > :cat',
                        'cat=0'
                    );

                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $Categories) {
                            \extract($Categories);
                            echo \sprintf("<option value='%s'>%s</option>", $presential_cat_id, $presential_cat_title);
                        }
                    }
                ?>
				<option value="#">Indisponíveis</option>
			</select>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>
</header>

<div class="dashboard_content">

    <?php

        $Page = \filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Pager = new Pager('dashboard.php?wc=albums/home&page=', '<<', '>>', 5);
        $Pager->exePager($Page, 12);
        $Read->exeRead(
            DB_ALBUMS,
            \sprintf(
                'WHERE 1 = 1 %s %s ORDER BY album_created DESC LIMIT :limit OFFSET :offset',
                $WhereString,
                $WhereOpt
            ),
            \sprintf('limit=%d&offset=%d', $Pager->getLimit(), $Pager->getOffset())
        );
        if (!$Read->getResult()) {
            $Pager->returnPage();
            echo Check::erro(
                \sprintf(
                    "<span class='al_center icon-notification'>Ainda não existem álbuns cadastrados %s. Comece agora mesmo criando seu primeiro álbum!</span>",
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Albums) {
                \extract($Albums);
                $AlbImage = ($album_cover && \file_exists('../uploads/albuns/' . $album_cover) && !\is_dir(
                    '../uploads/albuns/' . $album_cover
                ) ? 'uploads/albuns/' . $album_cover : 'admin/_img/no_image.jpg');
                $AlbTitle = ($album_title ? Check::Chars(
                    $album_title,
                    45
                ) : 'Edite este álbum para coloca-lo a Website!');
                $AlbClass = (1 != $album_status ? 'inactive' : '');

                echo "<article class='box box25 single_pdt {$AlbClass}' id='{$album_id}'>
            <div class='single_pdt_thumb'>
            <img title='{$AlbTitle}' alt='{$AlbTitle}' src='../tim.php?src={$AlbImage}&w=1200&h=628'/>
                <header>
                  <h1><a target='_blank' href='" . BASE . \sprintf(
                        "/album/%s' title='Ver %s no site'>%s</a></h1>",
                        $album_name,
                        $AlbTitle,
                        $AlbTitle
                    );

                $Read->fullRead(
                    'SELECT presential_cat_title FROM ' . DB_PRESENTIAL_CATEGORIES . ' WHERE presential_cat_id = :cat',
                    'cat=' . $album_category
                );
                $Category = ($Read->getResult() ? $Read->getResult()[0]['presential_cat_title'] : 'indefinida');

                $Read->fullRead(
                    'SELECT presential_cat_title FROM ' . DB_PRESENTIAL_CATEGORIES . ' WHERE presential_cat_id = :cat',
                    'cat=' . $album_subcategory
                );
                $SubCategory = ($Read->getResult() ? $Read->getResult()[0]['presential_cat_title'] : 'indefinida');

                echo "</header>
            </div>
            <div class='box_content'>
                <div class='single_pdt_info wc_normalize_height'>
                    <p>Em: <b>{$Category}</b> &raquo; <b>{$SubCategory}</b></p>
                </div>
            </div>
            <div class='single_pdt_actions'>
                <a title='Editar álbum' href='dashboard.php?wc=albums/create&id={$album_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                <span rel='single_pdt' class='j_delete_action icon-cancel-circle btn btn_red' id='{$album_id}'>Excluir</span>
                <span rel='single_pdt' callback='Albums' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$album_id}'>Remover Álbum?</span>
            </div>
        </article>";
            }

            $Pager->exePaginator(DB_ALBUMS);
            echo $Pager->getPaginator();
        }
    ?>
</div>
