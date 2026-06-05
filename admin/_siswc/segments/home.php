<?php

    use App\Conn\Create;
    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_SEGMENTS;
    if (!APP_SEGMENTS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO DELETE SEGMENT TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(DB_SEG, 'WHERE seg_title IS NULL AND seg_description IS NULL and seg_status = :st', 'st=0');

        // AUTO TRASH IMAGES
        $Read->fullRead(
            'SELECT image FROM ' . DB_SEG_IMAGE . ' WHERE segment_id NOT IN(SELECT seg_id FROM ' . DB_SEG . ')'
        );
        if ($Read->getResult()) {
            $Delete->exeDelete(
                DB_SEG_IMAGE,
                'WHERE id >= :id AND segment_id NOT IN(SELECT seg_id FROM ' . DB_SEG . ')',
                'id=1'
            );
            foreach ($Read->getResult() as $ImageRemove) {
                if (
                    file_exists('../uploads/' . $ImageRemove['image']) && !is_dir(
                        '../uploads/' . $ImageRemove['image']
                    )
                ) {
                    unlink('../uploads/' . $ImageRemove['image']);
                }
            }
        }
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $S = filter_input(INPUT_GET, 's', FILTER_DEFAULT);
    $O = filter_input(INPUT_GET, 'opt', FILTER_DEFAULT);

    $WhereString = (empty($S) ? '' : " AND (seg_title LIKE '%{$S}%' OR seg_description LIKE '%{$S}%') ");
    $WhereOpt = ((empty($O)) ? '' : ' AND (seg_status != 1) ');

    $Search = filter_input_array(INPUT_POST);
    if ($Search) {
        $S = urlencode((string)$Search['s']);
        $O = urlencode((string)$Search['opt']);
        header(sprintf('Location: dashboard.php?wc=segments/home&opt=%s&s=%s', $O, $S));

        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-books">Segmentos</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Segmentos
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchPosts" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" name="s" placeholder="Pesquisar:" style="width: 38%; margin-right: 3px;"/>
			<select name="opt" style="width: 45%; margin-right: 3px; padding: 5px 10px">
				<option value="">Todos</option>
				<option <?php
                    echo 'outsale' == $O ? "selected='selected'" : ''; ?> value="outsale">Indisponíveis
				</option>
			</select>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>

</header>
<div class="dashboard_content">
    <?php
        $RedirectOpt = ('' === $WhereOpt || '0' === $WhereOpt ? '' : '&opt=outsale');
        $Page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;
        $Pager = new Pager(sprintf('dashboard.php?wc=segments/home%s&page=', $RedirectOpt), '<<', '>>', 5);
        $Pager->exePager($Page, 12);
        $Read->exeRead(
            DB_SEG,
            sprintf('WHERE 1 = 1 %s %s ORDER BY seg_created DESC LIMIT :limit OFFSET :offset', $WhereString, $WhereOpt),
            sprintf('limit=%d&offset=%d', $Pager->getLimit(), $Pager->getOffset())
        );
        if (!$Read->getResult()) {
            $Pager->returnPage();
            echo Check::erro(
                sprintf(
                    "Ainda não existem segmentos cadastrados %s. Comece agora mesmo criando seu primeiro segmento!",
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Segments) {
                extract($Segments);
                $SegImage = ($seg_cover && file_exists('../uploads/' . $seg_cover) && !is_dir(
                    '../uploads/' . $seg_cover
                ) ? 'uploads/' . $seg_cover : 'admin/_img/no_image.jpg');
                $SegTitle = ($seg_title ? Check::chars($seg_title, 45) : 'Edite este segmento!');
                $SegStatus = (1 != $seg_status ? 'inactive' : '');
                echo "<article class='box box25 single_pdt {$SegStatus}' id='{$seg_id}'>
                    <div class='single_pdt_thumb'>
                        <img title='{$SegTitle}' alt='{$SegTitle}' src='../tim.php?src={$SegImage}&w=" . THUMB_W . '&h=' . THUMB_H . "'/>
                            <header>
                                <h1><a target='_blank' href='" . BASE . sprintf(
                        "/segmento/%s' title='Ver %s no site'>%s</a></h1>",
                        $seg_name,
                        $SegTitle,
                        $SegTitle
                    );

                echo "</header>
                    </div>
                        <div class='single_pdt_actions'>
                            <a title='Editar produto' href='dashboard.php?wc=segments/create&id={$seg_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                            <span rel='single_pdt' class='j_delete_action icon-cancel-circle btn btn_red' id='{$seg_id}'>Excluir</span>
                            <span rel='single_pdt' callback='Segments' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$seg_id}'>Remover Segmento?</span>
                        </div>
                    </article>";
            }

            $Pager->exePaginator(DB_SEG, sprintf('WHERE 1 = 1 %s %s', $WhereString, $WhereOpt));
            echo $Pager->getPaginator();
        }
    ?>
</div>
