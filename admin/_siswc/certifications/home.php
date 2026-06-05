<?php

    use App\Conn\Create;
    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_CERTIFICATIONS;
    if (!APP_CERTIFICATIONS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO DELETE CERTIFICATION TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(
            DB_CERT,
            'WHERE cert_title IS NULL AND cert_description IS NULL and cert_status = :st',
            'st=0'
        );
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $S = filter_input(INPUT_GET, 's', FILTER_DEFAULT);
    $S = is_string($S) ? trim($S) : null;
    $WhereString = (null === $S || '' === $S || '0' === $S ? '' : "AND (cert_title LIKE '%{$S}%' OR cert_description LIKE '%{$S}%')");

    $Search = filter_input_array(INPUT_POST);
    if ($Search) {
        $S = urlencode((string)$Search['s']);
        header(sprintf('Location: dashboard.php?wc=certifications/home&s=%s', $S));

        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-books">Certificações</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Certificações
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchPosts" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" name="s" placeholder="Pesquisar:" style="width: 38%; margin-right: 3px;"/>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>

</header>
<div class="dashboard_content">
    <?php
        $Page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;
        $Pager = new Pager('dashboard.php?wc=certifications/home%s&page=', '<<', '>>', 5);
        $Pager->exePager($Page, 12);
        $Read->exeRead(
            DB_CERT,
            sprintf('WHERE 1 = 1 %s ORDER BY cert_created DESC LIMIT :limit OFFSET :offset', $WhereString),
            sprintf('limit=%d&offset=%d', $Pager->getLimit(), $Pager->getOffset())
        );
        if (!$Read->getResult()) {
            $Pager->returnPage();
            echo Check::erro(
                sprintf(
                    'Ainda não existem certificações cadastradas %s. Comece agora mesmo criando sua primeira certificação!',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Certificações) {
                extract($Certificações);
                $CertImage = ($cert_cover && file_exists('../uploads/' . $cert_cover) && !is_dir(
                    '../uploads/' . $cert_cover
                ) ? 'uploads/' . $cert_cover : 'admin/_img/no_image.jpg');
                $CertTitle = ($cert_title ? Check::Chars($cert_title, 45) : 'Edite esta certificação!');
                $CertStatus = (1 != $cert_status ? 'inactive' : '');
                echo "<article class='box box25 single_pdt {$CertStatus}' id='{$cert_id}'>
                    <div class='single_pdt_thumb'>
                        <img title='{$CertTitle}' alt='{$CertTitle}' src='../tim.php?src={$CertImage}&w=" . THUMB_W . '&h=' . THUMB_H . "'/>
                            <header>
                                <h1><a target='_blank' href='" . BASE . sprintf(
                        "/certificacao/%s' title='Ver %s no site'>%s</a></h1>",
                        $cert_name,
                        $CertTitle,
                        $CertTitle
                    );

                echo "</header>
                    </div>
                        <div class='single_pdt_actions'>
                            <a title='Editar certificação' href='dashboard.php?wc=certifications/create&id={$cert_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                            <span rel='single_pdt' class='j_delete_action icon-cancel-circle btn btn_red' id='{$cert_id}'>Excluir</span>
                            <span rel='single_pdt' callback='Certifications' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$cert_id}'>Remover Certificação?</span>
                        </div>
                    </article>";
            }

            $Pager->exePaginator(DB_CERT, sprintf('WHERE 1 = 1 %s', $WhereString));
            echo $Pager->getPaginator();
        }
    ?>
</div>
