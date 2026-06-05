<?php

    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = 6;
    if (!APP_SEARCH || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    $SearchForm = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    if (!empty($SearchForm['inicio']) && !empty($SearchForm['fim'])) {
        $inicio = Check::Data($SearchForm['inicio']);
        $fim = Check::Data($SearchForm['fim']);
        $Where = 'AND search_date BETWEEN :inicio AND :fim';
        $ParseString = sprintf('inicio=%s&fim=%s', $inicio, $fim);
    } else {
        $Where = '';
        $ParseString = '';
    }
?>
<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-search">Relatório de Pesquisas no Blog</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Pesquisas no Blog
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchPosts" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="text" class="jwc_datepicker" data-timepicker="false" name="inicio"
			       placeholder="Data de Início:" style="width: 38%; margin-right: 3px;"/>
			<input type="text" class="jwc_datepicker" data-timepicker="false" name="fim" placeholder="Data de Término:"
			       style="width: 38%; margin-right: 3px;"/>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>
</header>
<div class="dashboard_content">

	<div class="box box100 dashboard_search">
		<div class="panel_header alert">
			<h2 class="icon-search">Últimas Pesquisas:</h2>
		</div>
		<div class="panel wc_onlinenow">
            <?php
                $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                $Page = $getPage ?? 1;
                $Pager = new Pager('dashboard.php?wc=searchnow&page=', '<<', '>>', 5);

                $Pager->exePager($Page, 10);
                $Read->exeRead(
                    DB_SEARCH,
                    sprintf(
                        ' WHERE 1 = 1 %s AND search_publish IS NULL AND search_origin = :post ORDER BY search_commit DESC, search_count DESC LIMIT :limit OFFSET :offset',
                        $Where
                    ),
                    sprintf('post=POST&limit=%d&offset=%d&%s', $Pager->getLimit(), $Pager->getOffset(), $ParseString)
                );

                if (!$Read->getResult()) {
                    $Pager->returnPage();
                    echo Check::erro(
                        '<span>Seus usuários ainda não pesquisaram em seu Blog. Assim que isso acontecer você poderá receber dicas de conteúdo pelas pesquisas realizadas!</span>',
                        E_USER_NOTICE
                    );
                } else {
                    foreach ($Read->getResult() as $Search) {
                        extract($Search);

                        $Read->fullRead(
                            'SELECT post_id FROM ' . DB_POSTS . " WHERE post_status = 1 AND post_date <= NOW() AND (post_title LIKE '%' :s '%' OR post_subtitle LIKE '%' :s '%' OR post_tags LIKE '%' :s '%' OR post_month = :s OR post_author = :s)",
                            's=' . $search_key
                        );
                        $ResultPosts = $Read->getRowCount();

                        echo " <article id='{$search_id}'>
              <h1 class='icon-search'><a href='dashboard.php?wc=posts/search&s=" . urlencode(
                                (string)$search_key
                            ) . "' title='Ver resultados'>" . ('month' == $search_parse
                                ? '<b>Mês: </b>' . getWcMonths($search_key) : $search_key) . '</a></h1>
                           <p>DIA ' . date('d/m/Y H\hi', strtotime((string)$search_date)) . '</p>
                           <p>' . str_pad((string)$search_count, 4, 0, STR_PAD_LEFT) . ' VEZES</p>
                           <p>' . str_pad((string)$Read->getRowCount(), 4, 0, STR_PAD_LEFT) . " RESULTADOS</p>
                           <p>
                                    <button class='btn btn_green icon-notext icon-checkmark wc_tooltip j_wc_action' data-callback='Search' data-callback-action='publish' data-value='{$search_id}'><span class='wc_tooltip_balloon'>Publicar</span></button>
                                    <button class='btn btn_red icon-notext icon-cross wc_tooltip j_wc_action' data-callback='Search' data-callback-action='delete' data-value='{$search_id}'><span class='wc_tooltip_balloon'>Deletar</span></button>
                            </p>
                        </article>
                        ";
                    }
                }
            ?>
			<div class="clear"></div>
		</div>
	</div>

    <?php
        $Pager->exePaginator(
            DB_SEARCH,
            sprintf(' WHERE 1 = 1 %s AND search_publish IS NULL AND search_origin = :post ', $Where),
            'post=POST&' . $ParseString
        );
        echo $Pager->getPaginator();
    ?>
</div>
