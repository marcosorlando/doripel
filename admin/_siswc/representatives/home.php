<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_REPRESENTATIVES;
    if (!APP_REPRESENTATIVES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(DB_REPRESENTATIVES, 'WHERE rep_company IS NULL and rep_name IS NULL', '');
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    $S = filter_input(INPUT_GET, 's', FILTER_DEFAULT);

    $Search = filter_input_array(INPUT_POST);

    if ($Search && (isset($Search['s']))) {
        $S = (isset($Search['s']) ? Check::safeUrlEncode($Search['s']) : '');
        header(sprintf('Location: dashboard.php?wc=representatives/home&s=%s', $S));
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-pen">Representantes</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="Todos os Representantes" href="dashboard.php?wc=representatives/home">Representantes</a>
            <?php
                echo $S ? sprintf("<span class='crumb'>/</span> <span class='icon-search'>%s</span>", $S) : ''; ?>
		</p>
	</div>

	<div class="dashboard_header_search">

		<form style="width: 100%; display: inline-block;" name="searchCategoriesPost" action="" method="post"
		      enctype="multipart/form-data" class="ajax_off">
			<input type="search" value="<?php
                echo $S; ?>" name="s" placeholder="Pesquisar:"
			       style="width: 38%; margin-right: 3px;">
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>
</header>

<div class="dashboard_content">
    <?php

        $getPage = filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
        $Page = $getPage ?? 1;
        $Paginator = new Pager(sprintf('dashboard.php?wc=representatives/home&s=%s&pg=', $S), '<<', '>>', 5);
        $Paginator->exePager($Page, 100);

        $WhereString = [];
        if (!empty($S)) {
            $WhereString[0] = " AND (rep_name LIKE '%' :s '%' OR rep_company LIKE '%' :s '%')";
            $WhereString[1] = '&s=' . $S;
        } else {
            $WhereString[0] = '';
            $WhereString[1] = '';
        }

        $Read->fullRead(
            'SELECT * FROM ' . DB_REPRESENTATIVES . sprintf(
                ' WHERE 1=1 %s ORDER BY rep_name ASC ',
                $WhereString[0]
            ) . ' LIMIT :limit OFFSET :offset',
            sprintf('limit=%d&offset=%d%s', $Paginator->getLimit(), $Paginator->getOffset(), $WhereString[1])
        );

        if (!$Read->getResult()) {
            $Paginator->returnPage();
            echo Check::erro(
                sprintf(
                    '<span>Ainda não existem representantes cadastrados %s. Comece agora mesmo cadastrando o primeiro representante!</span>',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Representative) {
                extract($Representative);

                $RepresentativeCover = (file_exists('../uploads/' . $rep_image) && !is_dir(
                    '../uploads/' . $rep_image
                ) ? 'uploads/' . $rep_image : 'admin/_img/no_image.jpg');

                $rep_company = (empty($rep_company) ? 'Edite esse rascunho para poder exibir como representante em seu site!' : $rep_company);

                echo "
                    <article class='box box25 post_single' callback='Representative' callback_action='rep_name' id='{$rep_id}'>
                        <div class='post_single_cover box_content'>
                                        
                            <div class='post_single_content wc_normalize_height'>
                                <h1 class='title' style='font-size:1.25em;'>{$rep_company}</h1>
                                <p>{$rep_city} - {$rep_uf}</p>
                            </div>                
                            <div class='post_single_actions'>
                                <a title='Editar Representante' href='dashboard.php?wc=representatives/create&id={$rep_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                                    <span rel='post_single' class='j_delete_action icon-cancel-circle btn btn_red' id='{$rep_id}'>Excluir</span>
                                    <span rel='post_single' callback='Representatives' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$rep_id}'>Remover Representante?</span>                    
                            </div>               
                        </div> 
                    </article>
                ";
            }

            $Paginator->exePaginator(
                DB_REPRESENTATIVES,
                "WHERE (rep_name LIKE '%' :s '%' OR rep_company LIKE '%' :s '%')",
                's=' . $S
            );
            echo $Paginator->getPaginator();
        }
    ?>
</div>
