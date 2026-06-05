<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    if (!APP_PARTNERS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < LEVEL_WC_PARTNERS) {
        Check::accessBlocked();
    }
    $Read ??= new Read();
    // AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();

        // AUTO TRASH IMAGES
        $Read->fullRead(
            'SELECT partner_image FROM ' . DB_PARTNERS . ' WHERE partner_name IS NULL AND partner_page IS NULL ',
            ''
        );

        if ($Read->getResult()) {
            extract($Read->getResult());

            if (file_exists("../uploads/{$partner_image}") && !is_dir("../uploads/{$partner_image}")) {
                unlink("../uploads/{$partner_image}");
            }
        }
        $Delete->exeDelete(DB_PARTNERS, 'WHERE partner_image IS NULL AND partner_date IS NULL', '');
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    $S = filter_input(INPUT_GET, 's', FILTER_DEFAULT);
    $Search = filter_input_array(INPUT_POST);
    if ($Search && (isset($Search['s']) || isset($Search['status']))) {
        $S = (isset($Search['s']) ? urlencode($Search['s']) : $S);
        $SearchCat = (empty($Search['searchcat']) ? null : $Search['searchcat']);
        header(sprintf('Location: dashboard.php?wc=partners/home&s=%s&cat=%s&tag=%s', $S, $SearchCat, $T));
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-user-check">Parceiros</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="Todos os Parceiros" href="dashboard.php?wc=partners/home">Parceiros</a>
            <?php
                echo $S ? sprintf("<span class='crumb'>/</span> <span class='icon-search'>%s</span>", $S) : ''; ?>
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchCategoriesPost" action="" method="post"
		      enctype="multipart/form-data" class="ajax_off">
			<input type="search" value="<?= $S; ?>" name="s" placeholder="Pesquisar:">
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>
</header>

<div class="dashboard_content">
    <?php
        $getPage = filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
        $Page = $getPage ?? 1;
        $Paginator = new Pager(sprintf('dashboard.php?wc=partners/home&s=%s&pg=', $S), '<<', '>>', 5);
        $Paginator->exePager($Page, 100);

        if (!empty($S)) {
            $WhereString[0] = "AND (partner_name LIKE '%' :s '%')";
            $WhereString[1] = '&s=' . $S;
        } else {
            $WhereString[0] = '';
            $WhereString[1] = '';
        }

        $Read->fullRead(
            'SELECT * FROM ' . DB_PARTNERS . ' WHERE 1=1 '
            . ($WhereString[0] . ' ')
            . 'ORDER BY partner_name ASC '
            . 'LIMIT :limit OFFSET :offset',
            sprintf('limit=%d&offset=%d%s', $Paginator->getLimit(), $Paginator->getOffset(), $WhereString[1])
        );

        if (!$Read->getResult()) {
            $Paginator->returnPage();
            echo Check::erro(
                sprintf(
                    "Ainda não existem parceiros cadastrados %s. Comece agora mesmo criando seu primeiro parceiro!",
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Partner) {
                extract($Partner);

                $PartnerCover = (file_exists('../uploads/' . $partner_image) && !is_dir(
                    '../uploads/' . $partner_image
                ) ? 'uploads/' . $partner_image : 'admin/_img/no_image.jpg');

                $partner_name = (empty($partner_name) ? 'Edite esse rascunho para poder exibir como parceiro em seu site!' : $partner_name);

                $CourseDragAndDrop = (empty($segment_title) ? 'wc_draganddrop' : null);

                echo "<article class='box box25 post_single {$CourseDragAndDrop}' callback='Depositions' callback_action='partner_order' id='{$partner_id}'>
                <div class='post_single_cover box_content'>
                   <img alt='{$partner_name}' title='{$partner_name}' src='../tim.php?src={$PartnerCover}&w=300&h=auto'/></a>

                <div class='post_single_content wc_normalize_height'>
                    <h1 class='title font_medium'>{$partner_name}</h1>
                </div>

                <div class='post_single_actions'>
                    <a title='Editar Prceiro' href='dashboard.php?wc=partners/create&id={$partner_id}' class='post_single_center btn btn_edit'>Editar</a>
                    <span rel='post_single' class='j_delete_action btn btn_delete' id='{$partner_id}'>Deletar</span>
                    <span rel='post_single' callback='Partners' callback_action='delete' class='j_delete_action_confirm btn btn_confirm' style='display: none' id='{$partner_id}'>Excluir?</span>

                </div>
            </article>";
            }

            $Paginator->exePaginator(
                DB_PARTNERS,
                "WHERE (partner_name LIKE '%' :s '%')",
                's=' . $S
            );
            echo $Paginator->getPaginator();
        }
    ?>
</div>
