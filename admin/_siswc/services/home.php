<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_SERVICES;
    if (!APP_SERVICES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO DELETE SERVICE TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(DB_SVC, 'WHERE svc_title IS NULL AND svc_description IS NULL and svc_status = :st', 'st=0');
        $Read ??= new Read();
        // AUTO TRASH IMAGES
        $Read->fullRead(
            'SELECT image FROM ' . DB_SVC_IMAGE . ' WHERE svc_id NOT IN(SELECT svc_id FROM ' . DB_SVC . ')'
        );
        if ($Read->getResult()) {
            $Delete->exeDelete(
                DB_SVC_IMAGE,
                'WHERE id >= :id AND service_id NOT IN(SELECT svc_id FROM ' . DB_SVC . ')',
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

    $S = filter_input(INPUT_GET, 's');
    $O = filter_input(INPUT_GET, 'opt');

    $WhereString = (empty($S) ? '' : " AND (svc_title LIKE '%{$S}%' OR svc_description LIKE '%{$S}%') ");
    $WhereOpt = ((empty($O)) ? '' : ' AND (svc_status != 1) ');

    $Search = filter_input_array(INPUT_POST);
    if ($Search) {
        $S = urlencode((string)$Search['s']);
        $O = urlencode((string)$Search['opt']);
        header(sprintf('Location: dashboard.php?wc=services/home&opt=%s&s=%s', $O, $S));

        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-hammer2">Processos</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Processos
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
        $Pager = new Pager(sprintf('dashboard.php?wc=services/home%s&page=', $RedirectOpt), '<<', '>>', 5);
        $Pager->exePager($Page, 12);
        $Read->exeRead(
            DB_SVC,
            sprintf('WHERE 1 = 1 %s %s ORDER BY svc_created DESC LIMIT :limit OFFSET :offset', $WhereString, $WhereOpt),
            sprintf('limit=%d&offset=%d', $Pager->getLimit(), $Pager->getOffset())
        );
        if (!$Read->getResult()) {
            $Pager->returnPage();
            echo Check::erro(
                sprintf(
                    'Ainda não existem serviços cadastrados %s. Comece agora mesmo criando seu primeiro serviço!',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Services) {
                extract($Services);
                $SvcImage = ($svc_cover && file_exists('../uploads/' . $svc_cover) && !is_dir(
                    '../uploads/' . $svc_cover
                ) ? 'uploads/' . $svc_cover : 'admin/_img/no_image.jpg');
                $SvcTitle = ($svc_title ? Check::chars($svc_title, 45) : 'Edite este serviço para coloca-lo a venda!');
                $SvcStatus = (1 != $svc_status ? 'inactive' : '');
                echo "<article class='box box25 single_pdt {$SvcStatus}' id='{$svc_id}'>
                    <div class='single_pdt_thumb'>
                        <img title='{$SvcTitle}' alt='{$SvcTitle}' src='../tim.php?src={$SvcImage}&w=" . THUMB_W . '&h=' . THUMB_H . "'/>
                            <header>
                                <h1><a target='_blank' href='" . BASE . sprintf(
                        "/servico/%s' title='Ver %s no site'>%s</a></h1>",
                        $svc_name,
                        $SvcTitle,
                        $SvcTitle
                    );

                echo "</header></div>
                        <div class='single_pdt_actions'>
                            <a title='Editar serviço' href='dashboard.php?wc=services/create&id={$svc_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                            <span rel='single_pdt' class='j_delete_action icon-cancel-circle btn btn_red' id='{$svc_id}'>Excluir</span>
                            <span rel='single_pdt' callback='Services' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$svc_id}'>Remover Serviço?</span>
                        </div>
                    </article>";
            }

            $Pager->exePaginator(DB_SVC, sprintf('WHERE 1 = 1 %s %s', $WhereString, $WhereOpt));
            echo $Pager->getPaginator();
        }
    ?>
</div>
